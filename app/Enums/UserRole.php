<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Pelanggan = 'pelanggan';
    case Mekanik = 'mekanik';
}
