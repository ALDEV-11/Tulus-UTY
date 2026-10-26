<?php

namespace Database\Seeders;

use App\Models\MerkMotor;
use Illuminate\Database\Seeder;

class MerkMotorSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Honda', 'Yamaha', 'Suzuki', 'Kawasaki', 'Vespa', 'KTM', 'Benelli', 'BMW Motorrad', 'Piaggio', 'TVS'] as $nama) {
            MerkMotor::create(['nama' => $nama]);
        }
    }
}
