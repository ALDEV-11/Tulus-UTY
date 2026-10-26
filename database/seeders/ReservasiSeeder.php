<?php

namespace Database\Seeders;

use App\Enums\StatusReservasi;
use App\Models\Kendaraan;
use App\Models\Layanan;
use App\Models\Reservasi;
use App\Models\SlotWaktu;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservasiSeeder extends Seeder
{
    public function run(): void
    {
        $pelanggan = User::where('email', 'pelanggan@aldi-motor.test')->firstOrFail();
        $kendaraan1 = Kendaraan::where('plat_nomor', 'AB 1234 CD')->firstOrFail();
        $kendaraan2 = Kendaraan::where('plat_nomor', 'AB 5678 EF')->firstOrFail();
        $slots = SlotWaktu::orderBy('tanggal')->orderBy('jam_mulai')->get();

        $reservasi1 = Reservasi::create([
            'user_id' => $pelanggan->id, 'kendaraan_id' => $kendaraan1->id, 'id_slot' => $slots[1]->id,
            'kode' => 'RSV-DEMO-001', 'status' => StatusReservasi::Dikonfirmasi,
            'keluhan' => 'Mesin terasa kurang responsif',
        ]);
        $reservasi1->layanan()->attach([
            Layanan::where('nama', 'Servis Ringan')->firstOrFail()->id => ['jumlah' => 1],
            Layanan::where('nama', 'Ganti Oli Mesin')->firstOrFail()->id => ['jumlah' => 1],
        ]);

        $reservasi2 = Reservasi::create([
            'user_id' => $pelanggan->id, 'kendaraan_id' => $kendaraan2->id, 'id_slot' => $slots[5]->id,
            'kode' => 'RSV-DEMO-002', 'status' => StatusReservasi::MenungguKonfirmasi,
            'keluhan' => 'Ingin servis berkala 10.000 km',
        ]);
        $reservasi2->layanan()->attach(
            Layanan::where('nama', 'Servis Lengkap')->firstOrFail()->id,
            ['jumlah' => 1]
        );

        $reservasi3 = Reservasi::create([
            'user_id' => $pelanggan->id, 'kendaraan_id' => $kendaraan1->id, 'id_slot' => $slots[9]->id,
            'kode' => 'RSV-DEMO-003', 'status' => StatusReservasi::Selesai,
            'keluhan' => 'Penggantian kampas rem',
        ]);
        $reservasi3->layanan()->attach(
            Layanan::where('nama', 'Ganti Kampas Rem')->firstOrFail()->id,
            ['jumlah' => 1]
        );
    }
}
