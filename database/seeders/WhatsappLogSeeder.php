<?php

namespace Database\Seeders;

use App\Enums\JenisWhatsapp;
use App\Models\Reservasi;
use App\Models\User;
use App\Models\WhatsappLog;
use Illuminate\Database\Seeder;

class WhatsappLogSeeder extends Seeder
{
    public function run(): void
    {
        $pelanggan = User::where('email', 'pelanggan@aldi-motor.test')->firstOrFail();
        $reservasi1 = Reservasi::where('kode', 'RSV-DEMO-001')->firstOrFail();
        $reservasi2 = Reservasi::where('kode', 'RSV-DEMO-002')->firstOrFail();

        WhatsappLog::create([
            'user_id' => $pelanggan->id, 'reservasi_id' => $reservasi1->id,
            'jenis' => JenisWhatsapp::KonfirmasiReservasi, 'nomor_tujuan' => $pelanggan->no_hp,
            'pesan' => 'Reservasi RSV-DEMO-001 telah dikonfirmasi.', 'status' => 'terkirim',
            'terkirim_pada' => now(), 'respons' => 'Demo seed',
        ]);

        WhatsappLog::create([
            'user_id' => $pelanggan->id, 'reservasi_id' => $reservasi2->id,
            'jenis' => JenisWhatsapp::PengingatServis, 'nomor_tujuan' => $pelanggan->no_hp,
            'pesan' => 'Pengingat reservasi RSV-DEMO-002.', 'status' => 'menunggu',
        ]);
    }
}
