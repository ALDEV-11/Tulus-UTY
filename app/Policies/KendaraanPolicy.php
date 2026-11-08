<?php

namespace App\Policies;

use App\Models\Kendaraan;
use App\Models\User;

class KendaraanPolicy
{
    public function view(User $user, Kendaraan $kendaraan): bool
    {
        return $user->role->value === 'pelanggan'
            && $kendaraan->user_id === $user->id;
    }

    public function update(User $user, Kendaraan $kendaraan): bool
    {
        return $this->view($user, $kendaraan);
    }

    public function delete(User $user, Kendaraan $kendaraan): bool
    {
        return $this->view($user, $kendaraan);
    }
}
