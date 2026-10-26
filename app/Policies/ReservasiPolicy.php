<?php

namespace App\Policies;

use App\Models\Reservasi;
use App\Models\User;

class ReservasiPolicy
{
    public function view(User $user, Reservasi $reservasi): bool
    {
        return $user->role->value === 'pelanggan'
            && $reservasi->user_id === $user->id;
    }

    public function cancel(User $user, Reservasi $reservasi): bool
    {
        return $this->view($user, $reservasi)
            && $reservasi->status->value === 'menunggu_konfirmasi';
    }
}
