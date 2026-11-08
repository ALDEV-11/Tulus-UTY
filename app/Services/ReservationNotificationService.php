<?php

namespace App\Services;

use App\Enums\StatusReservasi;
use App\Models\Reservasi;
use Illuminate\Support\Collection;

class ReservationNotificationService
{
    public function payload(Reservasi $reservasi): array
    {
        $reservasi->loadMissing(['pelanggan', 'kendaraan.merk', 'slot', 'layanan']);
        $slot = $reservasi->slot;

        return [
            'id' => $reservasi->id,
            'kode' => $reservasi->kode,
            'pelanggan' => $reservasi->pelanggan?->name ?? 'Pelanggan',
            'kendaraan' => trim(($reservasi->kendaraan?->plat_nomor ?? '-').' '.($reservasi->kendaraan?->merk?->nama ?? '')),
            'tanggal' => $slot?->tanggal?->translatedFormat('d M Y') ?? '-',
            'jam' => $slot ? substr($slot->jam_mulai, 0, 5).' – '.substr($slot->jam_selesai, 0, 5) : '-',
            'layanan' => $reservasi->layanan->pluck('nama')->join(', '),
            'url' => route('admin.reservasi.index', ['q' => $reservasi->kode]),
            'created_at' => $reservasi->created_at?->translatedFormat('d M Y H:i') ?? now()->translatedFormat('d M Y H:i'),
        ];
    }

    public function pendingCount(): int
    {
        return Reservasi::query()
            ->where('status', StatusReservasi::MenungguKonfirmasi)
            ->count();
    }

    public function recentPending(int $limit = 5): Collection
    {
        return Reservasi::query()
            ->with(['pelanggan', 'kendaraan.merk', 'slot', 'layanan'])
            ->where('status', StatusReservasi::MenungguKonfirmasi)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
