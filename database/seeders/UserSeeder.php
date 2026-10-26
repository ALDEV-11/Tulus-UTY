<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@aldi-motor.test',
            'no_hp' => '6281234567890',
            'role' => UserRole::Admin,
            'password' => 'password',
        ]);

        User::create([
            'name' => 'Pelanggan Demo',
            'email' => 'pelanggan@aldi-motor.test',
            'no_hp' => '6281298765432',
            'role' => UserRole::Pelanggan,
            'password' => 'password',
        ]);
    }
}
