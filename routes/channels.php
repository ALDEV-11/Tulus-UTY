<?php

use App\Enums\UserRole;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin.reservations', function ($user) {
    return $user->role === UserRole::Admin;
});
