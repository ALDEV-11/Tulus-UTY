<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MerkMotorSeeder::class,
            LayananSeeder::class,
            SlotWaktuSeeder::class,
            KendaraanSeeder::class,
            ReservasiSeeder::class,
            ServisSeeder::class,
            WhatsappLogSeeder::class,
        ]);
    }
}
