<?php

namespace Database\Seeders;

use App\Models\Layanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['nama' => 'Servis Ringan', 'deskripsi' => 'Pemeriksaan dan penyetelan dasar', 'durasi_menit' => 60, 'interval_servis_km' => 2000],
            ['nama' => 'Servis Lengkap', 'deskripsi' => 'Pemeriksaan menyeluruh kendaraan', 'durasi_menit' => 120, 'interval_servis_km' => 5000],
            ['nama' => 'Ganti Oli Mesin', 'deskripsi' => 'Penggantian oli mesin', 'durasi_menit' => 30, 'interval_servis_km' => 2000],
            ['nama' => 'Ganti Oli Gardan', 'deskripsi' => 'Khusus motor matic', 'durasi_menit' => 30, 'interval_servis_km' => 4000],
            ['nama' => 'Tune Up', 'deskripsi' => 'Setel performa dan pembakaran mesin', 'durasi_menit' => 90, 'interval_servis_km' => 10000],
            ['nama' => 'Ganti Kampas Rem', 'deskripsi' => 'Penggantian kampas rem depan atau belakang', 'durasi_menit' => 60, 'interval_servis_km' => null],
        ] as $data) {
            Layanan::create($data);
        }
    }
}
