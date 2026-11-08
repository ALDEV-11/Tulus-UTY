<?php

namespace App\Services;

use App\Models\PengingatServis;
use App\Models\Servis;
use Illuminate\Support\Collection;

class ServiceReminderService
{
    public function createFor(Servis $servis): Collection
    {
        $servis->loadMissing(['reservasi.layanan', 'kendaraan']);

        if (! $servis->reservasi) {
            return collect();
        }

        $tanggalDasar = $servis->tanggal_selesai?->copy() ?? today();
        $odometer = $servis->odometerTampil();

        return $servis->reservasi->layanan
            ->map(function ($layanan) use ($servis, $tanggalDasar, $odometer) {
                if (! $layanan->interval_servis_hari && ! $layanan->interval_servis_km) {
                    return null;
                }

                return PengingatServis::query()->updateOrCreate(
                    [
                        'servis_id' => $servis->id,
                        'layanan_id' => $layanan->id,
                    ],
                    [
                        'kendaraan_id' => $servis->kendaraan_id,
                        'tanggal_berikutnya' => $layanan->interval_servis_hari
                            ? $tanggalDasar->copy()->addDays((int) $layanan->interval_servis_hari)->toDateString()
                            : null,
                        'kilometer_berikutnya' => $layanan->interval_servis_km && $odometer !== null
                            ? $odometer + (int) $layanan->interval_servis_km
                            : null,
                        'status' => 'menunggu',
                    ]
                );
            })
            ->filter()
            ->values();
    }
}
