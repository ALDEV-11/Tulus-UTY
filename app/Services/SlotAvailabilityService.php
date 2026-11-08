<?php

namespace App\Services;

use App\Models\SlotWaktu;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SlotAvailabilityService
{
    public const STATUS_TERSEDIA = 'tersedia';
    public const STATUS_HAMPIR_PENUH = 'hampir_penuh';
    public const STATUS_PENUH = 'penuh';

    public const STATUS_LABELS = [
        self::STATUS_TERSEDIA => 'Tersedia',
        self::STATUS_HAMPIR_PENUH => 'Hampir Penuh',
        self::STATUS_PENUH => 'Penuh',
    ];

    public const STATUS_BADGES = [
        self::STATUS_TERSEDIA => 'success',
        self::STATUS_HAMPIR_PENUH => 'warning',
        self::STATUS_PENUH => 'danger',
    ];

    public function forDate(CarbonInterface|string $date): Collection
    {
        $tanggal = $date instanceof CarbonInterface ? $date->toDateString() : $date;

        return $this->withActiveCount()
            ->where('aktif', true)
            ->whereDate('tanggal', $tanggal)
            ->orderBy('jam_mulai')
            ->get()
            ->map(fn (SlotWaktu $slot) => $this->decorate($slot));
    }

    public function upcomingAvailable(): Collection
    {
        return $this->withActiveCount()
            ->where('aktif', true)
            ->where(function (Builder $query) {
                $query->whereDate('tanggal', '>', today())
                    ->orWhere(function (Builder $query) {
                        $query->whereDate('tanggal', today())
                            ->whereTime('jam_mulai', '>', now()->format('H:i:s'));
                    });
            })
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get()
            ->map(fn (SlotWaktu $slot) => $this->decorate($slot))
            ->filter(fn (SlotWaktu $slot) => $slot->sisa > 0)
            ->values();
    }

    public function decorate(SlotWaktu $slot): SlotWaktu
    {
        $used = (int) ($slot->reservasi_aktif_count ?? $slot->reservasi()->aktif()->count());
        $remaining = max(0, (int) $slot->kapasitas - $used);

        $slot->setAttribute('terpakai', $used);
        $slot->setAttribute('sisa', $remaining);
        $slot->setAttribute('status_ketersediaan', $this->status((int) $slot->kapasitas, $used));
        $slot->setAttribute('lewat', $this->isPast($slot));

        return $slot;
    }

    public function status(int $capacity, int $used): string
    {
        $remaining = max(0, $capacity - $used);

        if ($remaining === 0) {
            return self::STATUS_PENUH;
        }

        $threshold = intdiv(max(0, $capacity), 2);

        return $threshold > 0 && $remaining <= $threshold
            ? self::STATUS_HAMPIR_PENUH
            : self::STATUS_TERSEDIA;
    }

    public function isPast(SlotWaktu $slot): bool
    {
        return $slot->tanggal->copy()->setTimeFromTimeString($slot->jam_mulai)->isPast();
    }

    public function summary(Collection $slots): array
    {
        return [
            'total' => $slots->count(),
            'tersedia' => $slots->where('status_ketersediaan', self::STATUS_TERSEDIA)->count(),
            'hampir_penuh' => $slots->where('status_ketersediaan', self::STATUS_HAMPIR_PENUH)->count(),
            'penuh' => $slots->where('status_ketersediaan', self::STATUS_PENUH)->count(),
        ];
    }

    private function withActiveCount(): Builder
    {
        return SlotWaktu::query()->withCount([
            'reservasi as reservasi_aktif_count' => fn (Builder $query) => $query->aktif(),
        ]);
    }
}
