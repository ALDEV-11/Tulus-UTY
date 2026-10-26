<?php

namespace App\Enums;

enum StatusServis: string
{
    case Dijadwalkan = 'dijadwalkan';
    case Dikerjakan = 'dikerjakan';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';
}
