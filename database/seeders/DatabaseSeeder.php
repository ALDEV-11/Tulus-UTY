<?php

namespace Database\Seeders;

use App\Enums\JenisWhatsapp;
use App\Enums\StatusReservasi;
use App\Enums\StatusServis;
use App\Enums\UserRole;
use App\Models\Kendaraan;
use App\Models\Layanan;
use App\Models\MerkMotor;
use App\Models\Reservasi;
use App\Models\Servis;
use App\Models\SlotWaktu;
use App\Models\User;
use App\Models\WhatsappLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'email' => 'admin@aldi-motor.test',
            'no_hp' => '6281234567890',
            'role' => UserRole::Admin,
            'password' => 'password',
        ]);

        $pelanggan = User::create([
            'name' => 'Pelanggan Demo',
            'email' => 'pelanggan@aldi-motor.test',
            'no_hp' => '6281298765432',
            'role' => UserRole::Pelanggan,
            'password' => 'password',
        ]);

        $merkNames = ['Honda', 'Yamaha', 'Suzuki', 'Kawasaki', 'Vespa', 'KTM', 'Benelli', 'BMW Motorrad', 'Piaggio', 'TVS'];
        $merks = collect($merkNames)->mapWithKeys(fn (string $nama) => [$nama => MerkMotor::create(['nama' => $nama])]);

        $layananData = [
            ['nama' => 'Servis Ringan', 'deskripsi' => 'Pemeriksaan dan penyetelan dasar', 'durasi_menit' => 60, 'interval_servis_km' => 2000],
            ['nama' => 'Servis Lengkap', 'deskripsi' => 'Pemeriksaan menyeluruh kendaraan', 'durasi_menit' => 120, 'interval_servis_km' => 5000],
            ['nama' => 'Ganti Oli Mesin', 'deskripsi' => 'Penggantian oli mesin', 'durasi_menit' => 30, 'interval_servis_km' => 2000],
            ['nama' => 'Ganti Oli Gardan', 'deskripsi' => 'Khusus motor matic', 'durasi_menit' => 30, 'interval_servis_km' => 4000],
            ['nama' => 'Tune Up', 'deskripsi' => 'Setel performa dan pembakaran mesin', 'durasi_menit' => 90, 'interval_servis_km' => 10000],
            ['nama' => 'Ganti Kampas Rem', 'deskripsi' => 'Penggantian kampas rem depan atau belakang', 'durasi_menit' => 60, 'interval_servis_km' => null],
        ];
        $layanans = collect($layananData)->mapWithKeys(fn (array $data) => [$data['nama'] => Layanan::create($data)]);

        $slots = collect();
        for ($day = 1; $day <= 14; $day++) {
            $date = Carbon::today()->addDays($day);
            foreach ([8, 10, 12, 14] as $hour) {
                $slots->push(SlotWaktu::create([
                    'tanggal' => $date,
                    'jam_mulai' => sprintf('%02d:00', $hour),
                    'jam_selesai' => sprintf('%02d:00', $hour + 2),
                    'kapasitas' => 2,
                ]));
            }
        }

        $kendaraan1 = Kendaraan::create([
            'user_id' => $pelanggan->id, 'merk_motor_id' => $merks['Honda']->id,
            'plat_nomor' => 'AB 1234 CD', 'model' => 'Vario 160', 'tahun' => 2023, 'kilometer' => 8200,
        ]);
        $kendaraan2 = Kendaraan::create([
            'user_id' => $pelanggan->id, 'merk_motor_id' => $merks['Yamaha']->id,
            'plat_nomor' => 'AB 5678 EF', 'model' => 'NMAX 155', 'tahun' => 2022, 'kilometer' => 12500,
        ]);

        $reservasi1 = Reservasi::create([
            'user_id' => $pelanggan->id, 'kendaraan_id' => $kendaraan1->id, 'id_slot' => $slots[1]->id,
            'kode' => 'RSV-DEMO-001', 'status' => StatusReservasi::Dikonfirmasi,
            'keluhan' => 'Mesin terasa kurang responsif',
        ]);
        $reservasi1->layanan()->attach([
            $layanans['Servis Ringan']->id => ['jumlah' => 1],
            $layanans['Ganti Oli Mesin']->id => ['jumlah' => 1],
        ]);

        $reservasi2 = Reservasi::create([
            'user_id' => $pelanggan->id, 'kendaraan_id' => $kendaraan2->id, 'id_slot' => $slots[5]->id,
            'kode' => 'RSV-DEMO-002', 'status' => StatusReservasi::MenungguKonfirmasi,
            'keluhan' => 'Ingin servis berkala 10.000 km',
        ]);
        $reservasi2->layanan()->attach($layanans['Servis Lengkap']->id, ['jumlah' => 1]);

        $reservasi3 = Reservasi::create([
            'user_id' => $pelanggan->id, 'kendaraan_id' => $kendaraan1->id, 'id_slot' => $slots[9]->id,
            'kode' => 'RSV-DEMO-003', 'status' => StatusReservasi::Selesai,
            'keluhan' => 'Penggantian kampas rem',
        ]);
        $reservasi3->layanan()->attach($layanans['Ganti Kampas Rem']->id, ['jumlah' => 1]);

        Servis::create([
            'reservasi_id' => $reservasi1->id, 'kendaraan_id' => $kendaraan1->id, 'mekanik_id' => $admin->id,
            'tanggal_masuk' => Carbon::today(), 'status' => StatusServis::Dikerjakan,
            'diagnosa' => 'Oli mesin sudah menurun kualitasnya', 'tindakan' => 'Ganti oli dan setel throttle body',
            'total_biaya' => 130000,
        ]);
        Servis::create([
            'reservasi_id' => $reservasi3->id, 'kendaraan_id' => $kendaraan1->id, 'mekanik_id' => $admin->id,
            'tanggal_masuk' => Carbon::yesterday(), 'tanggal_selesai' => Carbon::yesterday(),
            'status' => StatusServis::Selesai, 'diagnosa' => 'Kampas rem aus',
            'tindakan' => 'Ganti kampas rem depan', 'total_biaya' => 100000,
        ]);

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
