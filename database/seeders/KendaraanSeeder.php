<?php

namespace Database\Seeders;

use App\Models\Kendaraan;
use App\Models\MerkMotor;
use App\Models\User;
use Illuminate\Database\Seeder;

class KendaraanSeeder extends Seeder
{
    public function run(): void
    {
        $pelanggan = User::where('email', 'pelanggan@aldi-motor.test')->firstOrFail();

        Kendaraan::create([
            'user_id' => $pelanggan->id,
            'merk_motor_id' => MerkMotor::where('nama', 'Honda')->firstOrFail()->id,
            'plat_nomor' => 'AB 1234 CD',
            'model' => 'Vario 160',
            'tahun' => 2023,
            'kilometer' => 8200,
            'odometer_terakhir' => 8200,
        ]);

        Kendaraan::create([
            'user_id' => $pelanggan->id,
            'merk_motor_id' => MerkMotor::where('nama', 'Yamaha')->firstOrFail()->id,
            'plat_nomor' => 'AB 5678 EF',
            'model' => 'NMAX 155',
            'tahun' => 2022,
            'kilometer' => 12500,
            'odometer_terakhir' => 12500,
        ]);
    }
}
