<?php

namespace Database\Seeders;

use App\Models\SlotWaktu;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SlotWaktuSeeder extends Seeder
{
    public function run(): void
    {
        for ($day = 1; $day <= 14; $day++) {
            $date = Carbon::today()->addDays($day);

            foreach ([8, 10, 12, 14] as $hour) {
                SlotWaktu::create([
                    'tanggal' => $date,
                    'jam_mulai' => sprintf('%02d:00', $hour),
                    'jam_selesai' => sprintf('%02d:00', $hour + 2),
                    'kapasitas' => 2,
                ]);
            }
        }
    }
}
