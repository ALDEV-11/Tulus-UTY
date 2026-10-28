<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusReservasi;
use App\Enums\StatusServis;
use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Models\Reservasi;
use App\Models\Servis;
use App\Services\ServiceReminderService;
use App\Services\WhatsappLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ServisController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(StatusServis::class)],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', ...($request->filled('dari') ? ['after_or_equal:dari'] : [])],
        ]);

        $search = trim((string) ($filters['q'] ?? ''));
        $status = $filters['status'] ?? null;
        $dari = $filters['dari'] ?? null;
        $sampai = $filters['sampai'] ?? null;

        $servis = Servis::query()
            ->with([
                'reservasi.pelanggan',
                'reservasi.layanan',
                'kendaraan.merk',
                'mekanik',
                'pengingatServis',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->whereHas('reservasi', function ($query) use ($search) {
                        $query->where('kode', 'like', "%{$search}%")
                            ->orWhereHas('pelanggan', fn ($query) => $query
                                ->where('name', 'like', "%{$search}%")
                                ->orWhere('no_hp', 'like', "%{$search}%"));
                    })->orWhereHas('kendaraan', fn ($query) => $query
                        ->where('plat_nomor', 'like', "%{$search}%"));
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($dari, fn ($query) => $query->whereDate('tanggal_masuk', '>=', $dari))
            ->when($sampai, fn ($query) => $query->whereDate('tanggal_masuk', '<=', $sampai))
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $statusCounts = Servis::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn (Servis $servis) => [
                $servis->status->value => (int) $servis->total,
            ]);

        $statusLabels = [];
        $statusBadges = [];
        $statusIcons = [
            StatusServis::Menunggu->value => 'bx bx-time-five',
            StatusServis::Dikerjakan->value => 'bx bx-wrench',
            StatusServis::Selesai->value => 'bx bx-check-circle',
            StatusServis::Diambil->value => 'bx bx-package',
        ];

        $statusCounts = collect(StatusServis::cases())
            ->mapWithKeys(function (StatusServis $status) use ($statusCounts, &$statusLabels, &$statusBadges) {
                $statusLabels[$status->value] = $status->label();
                $statusBadges[$status->value] = $status->badge();

                return [$status->value => (int) ($statusCounts[$status->value] ?? 0)];
            });

        $hasFilter = $search !== '' || $status || $dari || $sampai;
        $subtitle = $hasFilter
            ? $servis->total().' servis sesuai filter.'
            : $servis->total().' servis terdaftar.';

        return view('admin.servis.index', [
            'servis' => $servis,
            'statusCounts' => $statusCounts,
            'statusLabels' => $statusLabels,
            'statusBadges' => $statusBadges,
            'statusIcons' => $statusIcons,
            'search' => $search,
            'status' => $status,
            'dari' => $dari,
            'sampai' => $sampai,
            'subtitle' => $subtitle,
        ]);
    }

    public function checkIn(Request $request, Reservasi $reservasi): RedirectResponse
    {
        $data = $request->validate([
            'odometer_awal' => ['required', 'integer', 'min:0', 'max:9999999'],
        ]);

        DB::transaction(function () use ($reservasi, $data) {
            $locked = Reservasi::query()->lockForUpdate()->findOrFail($reservasi->id);

            if ($locked->status !== StatusReservasi::Dikonfirmasi) {
                throw ValidationException::withMessages([
                    'reservasi' => 'Hanya reservasi berstatus dikonfirmasi yang dapat di-check in.',
                ]);
            }

            $sudahAdaServis = Servis::query()
                ->where('reservasi_id', $locked->id)
                ->lockForUpdate()
                ->exists();

            if ($sudahAdaServis) {
                throw ValidationException::withMessages([
                    'reservasi' => 'Reservasi ini sudah memiliki data servis.',
                ]);
            }

            Servis::create([
                'reservasi_id' => $locked->id,
                'kendaraan_id' => $locked->kendaraan_id,
                'tanggal_masuk' => today()->toDateString(),
                'status' => StatusServis::Menunggu,
                'odometer_awal' => $data['odometer_awal'],
            ]);

            $locked->update(['status' => StatusReservasi::CheckIn]);
        });

        return to_route('admin.servis.index')
            ->with('status', "Check in {$reservasi->kode} berhasil. Kendaraan masuk antrean servis.");
    }

    public function update(Request $request, Servis $servis): RedirectResponse
    {
        $data = $request->validate([
            'odometer' => ['nullable', 'integer', 'min:0', 'max:9999999'],
            'rincian_pekerjaan' => ['nullable', 'string', 'max:2000'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($servis, $data) {
            $locked = Servis::query()->lockForUpdate()->findOrFail($servis->id);

            if ($locked->status === StatusServis::Diambil) {
                throw ValidationException::withMessages([
                    'servis' => 'Catatan servis yang sudah diambil tidak dapat diubah.',
                ]);
            }

            $locked->update($data);
        });

        return back()->with('status', 'Catatan servis berhasil diperbarui.');
    }

    public function status(
        Request $request,
        Servis $servis,
        ServiceReminderService $reminders,
        WhatsappLogService $whatsapp,
    ): RedirectResponse {
        $data = $request->validate([
            'status' => ['required', Rule::enum(StatusServis::class)],
        ]);

        $tujuan = StatusServis::from($data['status']);
        $pesan = null;

        DB::transaction(function () use ($servis, $tujuan, $reminders, $whatsapp, &$pesan) {
            $locked = Servis::query()->lockForUpdate()->findOrFail($servis->id);
            $asal = $locked->status;

            if (! $asal->bisaBerpindahKe($tujuan)) {
                throw ValidationException::withMessages([
                    'status' => "Status servis hanya dapat berpindah dari {$asal->label()} ke "
                        .($asal->berikutnya()?->label() ?? 'tidak ada status berikutnya').'.',
                ]);
            }

            $attributes = ['status' => $tujuan];

            if ($tujuan === StatusServis::Dikerjakan) {
                $attributes['waktu_mulai'] = now();
            }

            if ($tujuan === StatusServis::Selesai) {
                $attributes['waktu_selesai'] = now();
                $attributes['tanggal_selesai'] = today()->toDateString();
            }

            $locked->update($attributes);
            $locked->loadMissing('kendaraan');

            if ($tujuan === StatusServis::Selesai && $locked->reservasi_id) {
                $reservasi = Reservasi::query()->find($locked->reservasi_id);

                if ($reservasi) {
                    $whatsapp->serviceCompleted($reservasi);
                }
            }

            if ($tujuan === StatusServis::Diambil) {
                $reservasi = $locked->reservasi_id
                    ? Reservasi::query()->lockForUpdate()->find($locked->reservasi_id)
                    : null;

                $reservasi?->update(['status' => StatusReservasi::Selesai]);

                $odometer = $locked->odometerTampil();

                if ($odometer !== null) {
                    Kendaraan::query()
                        ->whereKey($locked->kendaraan_id)
                        ->lockForUpdate()
                        ->update(['odometer_terakhir' => $odometer]);
                }

                $reminders->createFor($locked);
            }

            $pesan = "Status servis {$locked->kendaraan?->plat_nomor} diubah menjadi {$tujuan->label()}.";
        });

        return back()->with('status', $pesan);
    }
}
