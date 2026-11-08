<?php

namespace App\Http\Controllers\Customer;

use App\Enums\StatusReservasi;
use App\Events\ReservationCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ReservasiRequest;
use App\Models\Kendaraan;
use App\Models\Reservasi;
use App\Models\SlotWaktu;
use App\Services\WhatsappLogService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function store(ReservasiRequest $request, WhatsappLogService $whatsapp): RedirectResponse
    {
        $data = $request->validated();

        $reservasi = DB::transaction(function () use ($data, $request, $whatsapp) {
            $slot = SlotWaktu::query()->lockForUpdate()->findOrFail($data['id_slot']);
            $slotDateTime = $slot->tanggal->copy()->setTimeFromTimeString($slot->jam_mulai);

            if (! $slot->aktif || $slotDateTime->isPast()) {
                throw ValidationException::withMessages([
                    'id_slot' => 'Slot yang dipilih sudah tidak tersedia.',
                ]);
            }

            $activeReservations = $slot->reservasi()->aktif()->count();

            if ($activeReservations >= $slot->kapasitas) {
                throw ValidationException::withMessages([
                    'id_slot' => 'Slot yang dipilih sudah penuh. Silakan pilih jadwal lain.',
                ]);
            }

            $kendaraan = $this->resolveKendaraan($request, $data);

            $reservasi = Reservasi::create([
                'user_id' => $request->user()->id,
                'kendaraan_id' => $kendaraan->id,
                'id_slot' => $slot->id,
                'kode' => $this->generateKode(),
                'status' => StatusReservasi::MenungguKonfirmasi,
                'keluhan' => $data['keluhan'] ?? null,
            ]);

            $reservasi->layanan()->attach(
                collect($data['layanan'])
                    ->mapWithKeys(fn ($layananId) => [(int) $layananId => ['jumlah' => 1]])
                    ->all()
            );

            $whatsapp->reservationCreated($reservasi);

            return $reservasi;
        });

        $this->broadcastReservation($reservasi);

        return to_route('booking-saya')->with(
            'status',
            "Reservasi {$reservasi->kode} berhasil dibuat dan sedang menunggu konfirmasi admin."
        );
    }

    public function cancel(Reservasi $reservasi, WhatsappLogService $whatsapp): RedirectResponse
    {
        Gate::authorize('cancel', $reservasi);

        DB::transaction(function () use ($reservasi, $whatsapp) {
            $locked = Reservasi::query()->lockForUpdate()->findOrFail($reservasi->id);

            if (! in_array($locked->status->value, ['menunggu_konfirmasi', 'dikonfirmasi'], true)) {
                throw ValidationException::withMessages([
                    'reservasi' => 'Reservasi ini sudah tidak dapat dibatalkan.',
                ]);
            }

            $locked->update(['status' => StatusReservasi::Dibatalkan]);
            $whatsapp->reservationCancelled($locked, 'Dibatalkan oleh pelanggan');
        });

        return back()->with('status', "Reservasi {$reservasi->kode} berhasil dibatalkan.");
    }

    private function resolveKendaraan(ReservasiRequest $request, array $data): Kendaraan
    {
        if (! empty($data['kendaraan_id'])) {
            return Kendaraan::query()
                ->where('user_id', $request->user()->id)
                ->findOrFail($data['kendaraan_id']);
        }

        $baru = $data['kendaraan_baru'];

        try {
            return $request->user()->kendaraan()->create([
                'merk_motor_id' => $baru['merk_motor_id'],
                'plat_nomor' => $baru['plat_nomor'],
                'model' => $baru['model'],
                'tahun' => $baru['tahun'],
                'kilometer' => 0,
            ]);
        } catch (QueryException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062) {
                throw ValidationException::withMessages([
                    'kendaraan_baru.plat_nomor' => 'Nomor polisi sudah terdaftar.',
                ]);
            }

            throw $exception;
        }
    }

    private function generateKode(): string
    {
        do {
            $kode = 'RSV-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
        } while (Reservasi::query()->where('kode', $kode)->exists());

        return $kode;
    }

    private function broadcastReservation(Reservasi $reservasi): void
    {
        try {
            ReservationCreated::dispatch($reservasi);
        } catch (\Throwable $exception) {
            Log::warning('Gagal mengirim notifikasi reservasi ke Pusher.', [
                'reservasi_id' => $reservasi->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
