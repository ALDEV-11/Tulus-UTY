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
            'reservasi_id' => $reservasi1->id,
            'kendaraan_id' => $kendaraan->id,
            'mekanik_id' => $admin->id,
            'tanggal_masuk' => Carbon::today(),
            'status' => StatusServis::Dikerjakan,
            'odometer_awal' => 8200,
            'odometer' => 8205,
            'waktu_mulai' => Carbon::today()->setTime(9, 0),
            'rincian_pekerjaan' => 'Ganti oli mesin, setel throttle body',
            'catatan' => 'Gunakan oli 10W-30 dan periksa kondisi busi.',
            'total_biaya' => 130000,
        ]);

        Servis::create([
            'reservasi_id' => $reservasi3->id,
            'kendaraan_id' => $kendaraan->id,
            'mekanik_id' => $admin->id,
            'tanggal_masuk' => Carbon::yesterday(),
            'tanggal_selesai' => Carbon::yesterday(),
            'status' => StatusServis::Diambil,
            'odometer_awal' => 12500,
            'odometer' => 12510,
            'waktu_mulai' => Carbon::yesterday()->setTime(10, 0),
            'waktu_selesai' => Carbon::yesterday()->setTime(15, 30),
            'rincian_pekerjaan' => 'Ganti kampas rem depan',
            'catatan' => 'Kampas rem depan diganti, rem belakang masih layak.',
            'total_biaya' => 100000,
        ]);

        $kendaraan->update(['odometer_terakhir' => 12510]);
    }
}
