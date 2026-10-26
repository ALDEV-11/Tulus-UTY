<?php

namespace App\Enums;

enum StatusReservasi: string
{
    case MenungguKonfirmasi = 'menunggu_konfirmasi';
    case Dikonfirmasi = 'dikonfirmasi';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';
}
