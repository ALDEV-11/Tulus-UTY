<?php

namespace Database\Seeders;

use App\Enums\StatusServis;
use App\Models\Kendaraan;
use App\Models\Reservasi;
use App\Models\Servis;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ServisSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@aldi-motor.test')->firstOrFail();
        $kendaraan = Kendaraan::where('plat_nomor', 'AB 1234 CD')->firstOrFail();
        $reservasi1 = Reservasi::where('kode', 'RSV-DEMO-001')->firstOrFail();
        $reservasi3 = Reservasi::where('kode', 'RSV-DEMO-003')->firstOrFail();

        Servis::create([
            'reservasi_id' => $reservasi1->id, 'kendaraan_id' => $kendaraan->id, 'mekanik_id' => $admin->id,
            'tanggal_masuk' => Carbon::today(), 'status' => StatusServis::Dikerjakan,
            'diagnosa' => 'Oli mesin sudah menurun kualitasnya', 'tindakan' => 'Ganti oli dan setel throttle body',
            'total_biaya' => 130000,
        ]);

        Servis::create([
            'reservasi_id' => $reservasi3->id, 'kendaraan_id' => $kendaraan->id, 'mekanik_id' => $admin->id,
            'tanggal_masuk' => Carbon::yesterday(), 'tanggal_selesai' => Carbon::yesterday(),
            'status' => StatusServis::Selesai, 'diagnosa' => 'Kampas rem aus',
            'tindakan' => 'Ganti kampas rem depan', 'total_biaya' => 100000,
        ]);
    }
}
