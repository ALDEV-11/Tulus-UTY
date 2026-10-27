<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JenisWhatsapp;
use App\Enums\StatusReservasi;
use App\Enums\StatusServis;
use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use App\Models\Servis;
use App\Models\User;
use App\Services\ReservationNotificationService;
use App\Services\WhatsappLogService;
use App\Support\WhatsappLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReservasiController extends Controller
{
    private const STATUS_LABELS = [
        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
        'dikonfirmasi' => 'Dikonfirmasi',
        'check_in' => 'Check In',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    private const STATUS_BADGES = [
        'menunggu_konfirmasi' => 'warning',
        'dikonfirmasi' => 'primary',
        'check_in' => 'info',
        'selesai' => 'success',
        'dibatalkan' => 'secondary',
    ];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(StatusReservasi::class)],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', ...($request->filled('dari') ? ['after_or_equal:dari'] : [])],
        ]);

        $search = trim((string) ($filters['q'] ?? ''));
        $status = $filters['status'] ?? null;
        $dari = $filters['dari'] ?? null;
        $sampai = $filters['sampai'] ?? null;

        $reservasis = Reservasi::query()
            ->with([
                'pelanggan',
                'kendaraan.merk',
                'slot',
                'layanan',
                'servis.mekanik',
                'whatsappLogs',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('kode', 'like', "%{$search}%")
                        ->orWhereHas('pelanggan', fn ($query) => $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('no_hp', 'like', "%{$search}%"))
                        ->orWhereHas('kendaraan', fn ($query) => $query
                            ->where('plat_nomor', 'like', "%{$search}%"));
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($dari, fn ($query) => $query->whereHas('slot', fn ($query) => $query
                ->whereDate('tanggal', '>=', $dari)))
            ->when($sampai, fn ($query) => $query->whereHas('slot', fn ($query) => $query
                ->whereDate('tanggal', '<=', $sampai)))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statusCounts = Reservasi::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()
            ->mapWithKeys(fn (Reservasi $reservasi) => [
                $reservasi->status->value => (int) $reservasi->total,
            ]);

        $statusCounts = collect(StatusReservasi::cases())
            ->mapWithKeys(fn (StatusReservasi $status) => [
                $status->value => (int) ($statusCounts[$status->value] ?? 0),
            ]);

        $hasFilter = $search !== '' || $status || $dari || $sampai;
        $subtitle = $hasFilter
            ? $reservasis->total().' reservasi sesuai filter.'
            : $reservasis->total().' reservasi terdaftar.';

        return view('admin.reservasi.index', [
            'reservasis' => $reservasis,
            'statusCounts' => $statusCounts,
            'statusLabels' => self::STATUS_LABELS,
            'statusBadges' => self::STATUS_BADGES,
            'search' => $search,
            'status' => $status,
            'dari' => $dari,
            'sampai' => $sampai,
            'subtitle' => $subtitle,
            'pusherConfigured' => $this->pusherConfigured(),
            'mekaniks' => User::query()
                ->where('role', 'admin')
                ->where('aktif', true)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function konfirmasi(Reservasi $reservasi, WhatsappLogService $whatsapp): RedirectResponse
    {
        if ($reservasi->status !== StatusReservasi::MenungguKonfirmasi) {
            return back()->withErrors(['reservasi' => 'Hanya reservasi yang menunggu konfirmasi yang dapat dikonfirmasi.']);
        }

        $reservasi->loadMissing('slot');

        if (! $reservasi->slot || ! $reservasi->slot->aktif) {
            return back()->withErrors(['reservasi' => 'Slot reservasi sudah tidak aktif.']);
        }

        $slotEnd = $reservasi->slot->tanggal->copy()->setTimeFromTimeString($reservasi->slot->jam_selesai);

        if ($slotEnd->isPast()) {
            return back()->withErrors(['reservasi' => 'Jadwal reservasi sudah lewat dan tidak dapat dikonfirmasi.']);
        }

        DB::transaction(function () use ($reservasi, $whatsapp) {
            $reservasi->update(['status' => StatusReservasi::Dikonfirmasi]);
            $whatsapp->reservationConfirmed($reservasi);
        });

        return back()->with('status', "Reservasi {$reservasi->kode} berhasil dikonfirmasi.");
    }

    public function batalkan(Request $request, Reservasi $reservasi, WhatsappLogService $whatsapp): RedirectResponse
    {
        if (! in_array($reservasi->status, [StatusReservasi::MenungguKonfirmasi, StatusReservasi::Dikonfirmasi], true)) {
            return back()->withErrors(['reservasi' => 'Reservasi ini tidak dapat dibatalkan.']);
        }

        $isRejection = $reservasi->status === StatusReservasi::MenungguKonfirmasi;

        $data = $request->validate([
            'alasan' => $isRejection
                ? ['required', 'string', 'max:500']
                : ['nullable', 'string', 'max:500'],
        ]);

        $reason = trim((string) ($data['alasan'] ?? ''));

        DB::transaction(function () use ($reservasi, $reason, $isRejection, $whatsapp) {
            $catatan = $reservasi->catatan;
            $prefix = $isRejection ? 'Ditolak admin: ' : 'Dibatalkan admin: ';

            if ($reason !== '') {
                $catatan = trim(($catatan ? $catatan."\n" : '').$prefix.$reason);
            }

            $reservasi->update([
                'status' => StatusReservasi::Dibatalkan,
                'catatan' => $catatan,
            ]);

            if ($isRejection) {
                $whatsapp->reservationRejected($reservasi, $reason);
            } else {
                $whatsapp->reservationCancelled($reservasi, $reason ?: 'Dibatalkan oleh admin');
            }
        });

        return back()->with('status', $isRejection
            ? "Reservasi {$reservasi->kode} berhasil ditolak."
            : "Reservasi {$reservasi->kode} berhasil dibatalkan.");
    }

    public function selesaikan(Request $request, Reservasi $reservasi, WhatsappLogService $whatsapp): RedirectResponse
    {
        $reservasi->loadMissing(['slot', 'servis']);

        if ($reservasi->status !== StatusReservasi::Dikonfirmasi) {
            return back()->withErrors([
                'reservasi' => 'Hanya reservasi yang sudah dikonfirmasi yang dapat diselesaikan.',
            ]);
        }

        $slotDate = $reservasi->slot?->tanggal?->toDateString();

        $data = $request->validate([
            'tanggal_selesai' => ['required', 'date', ...($slotDate ? ['after_or_equal:'.$slotDate] : [])],
            'mekanik_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(
                    fn ($query) => $query->where('role', 'admin')->where('aktif', true)
                ),
            ],
            'diagnosa' => ['nullable', 'string', 'max:2000'],
            'tindakan' => ['nullable', 'string', 'max:2000'],
            'total_biaya' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);

        DB::transaction(function () use ($reservasi, $data, $whatsapp) {
            $locked = Reservasi::query()->lockForUpdate()->findOrFail($reservasi->id);

            if ($locked->status !== StatusReservasi::Dikonfirmasi) {
                throw ValidationException::withMessages([
                    'reservasi' => 'Status reservasi berubah. Muat ulang halaman dan coba lagi.',
                ]);
            }

            $locked->loadMissing(['slot', 'kendaraan.merk', 'pelanggan', 'layanan']);

            $servis = Servis::query()
                ->where('reservasi_id', $locked->id)
                ->lockForUpdate()
                ->first();

            if ($servis && in_array($servis->status, [StatusServis::Selesai, StatusServis::Dibatalkan], true)) {
                throw ValidationException::withMessages([
                    'reservasi' => 'Servis untuk reservasi ini sudah selesai atau dibatalkan.',
                ]);
            }

            $attributes = [
                'status' => StatusServis::Selesai,
                'tanggal_selesai' => $data['tanggal_selesai'],
                'mekanik_id' => $data['mekanik_id'] ?? null,
                'diagnosa' => $data['diagnosa'] ?? null,
                'tindakan' => $data['tindakan'] ?? null,
                'total_biaya' => $data['total_biaya'] ?? 0,
            ];

            if ($servis) {
                $servis->update($attributes);
            } else {
                $servis = Servis::create($attributes + [
                    'reservasi_id' => $locked->id,
                    'kendaraan_id' => $locked->kendaraan_id,
                    'tanggal_masuk' => $locked->slot?->tanggal?->toDateString() ?? $data['tanggal_selesai'],
                ]);
            }

            $locked->update(['status' => StatusReservasi::Selesai]);

            $whatsapp->serviceCompleted($locked);
        });

        return back()->with('status', "Servis {$reservasi->kode} ditandai selesai.");
    }

    public function kirimWhatsapp(Request $request, Reservasi $reservasi, WhatsappLogService $whatsapp): RedirectResponse
    {
        $data = $request->validate([
            'jenis' => ['required', Rule::enum(JenisWhatsapp::class)],
        ]);

        $jenis = JenisWhatsapp::from($data['jenis']);

        $reservasi->loadMissing(['pelanggan', 'kendaraan.merk', 'slot', 'layanan', 'servis', 'whatsappLogs']);

        if ($error = $this->whatsappEligibilityError($reservasi, $jenis)) {
            return back()->withErrors(['whatsapp' => $error]);
        }

        $message = $whatsapp->messageFor($reservasi, $jenis);
        $link = WhatsappLink::for($reservasi->pelanggan?->no_hp, $message);

        if ($link === null) {
            return back()->withErrors([
                'whatsapp' => 'Nomor HP pelanggan belum diisi atau tidak valid. Perbarui data pelanggan terlebih dahulu.',
            ]);
        }

        $whatsapp->logManualSend($reservasi, $jenis, $message);

        return redirect()->away($link);
    }

    private function whatsappEligibilityError(Reservasi $reservasi, JenisWhatsapp $jenis): ?string
    {
        $servisSelesai = $reservasi->servis?->status === StatusServis::Selesai;

        return match ($jenis) {
            JenisWhatsapp::KonfirmasiReservasi => $reservasi->status === StatusReservasi::Dikonfirmasi
                ? null
                : 'Pesan konfirmasi hanya dapat dikirim untuk reservasi berstatus dikonfirmasi.',
            JenisWhatsapp::PenolakanReservasi => ($reservasi->status === StatusReservasi::Dibatalkan
                && $reservasi->whatsappLogs->contains(fn ($log) => $log->jenis === JenisWhatsapp::PenolakanReservasi))
                    ? null
                    : 'Pesan penolakan hanya dapat dikirim untuk reservasi yang ditolak.',
            JenisWhatsapp::ServisSelesai => $reservasi->status === StatusReservasi::Selesai && $servisSelesai
                ? null
                : 'Pesan servis selesai hanya dapat dikirim setelah servis berstatus selesai.',
            JenisWhatsapp::PengingatServis => $reservasi->status === StatusReservasi::Selesai && $servisSelesai
                ? null
                : 'Pengingat servis hanya dapat dikirim untuk reservasi yang servisnya sudah selesai.',
            default => 'Jenis pesan ini tidak dapat dikirim manual.',
        };
    }

    public function notifications(ReservationNotificationService $service): JsonResponse
    {
        return response()->json([
            'count' => $service->pendingCount(),
            'items' => $service->recentPending()
                ->map(fn (Reservasi $reservasi) => $service->payload($reservasi))
                ->values(),
        ])->header('Cache-Control', 'no-store');
    }

    private function pusherConfigured(): bool
    {
        return config('broadcasting.default') === 'pusher'
            && filled(config('broadcasting.connections.pusher.key'))
            && filled(config('broadcasting.connections.pusher.secret'))
            && filled(config('broadcasting.connections.pusher.app_id'));
    }
}
