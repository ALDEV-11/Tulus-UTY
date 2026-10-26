<?php

namespace App\Enums;

enum JenisWhatsapp: string
{
    case KonfirmasiReservasi = 'konfirmasi_reservasi';
    case PengingatServis = 'pengingat_servis';
    case ServisSelesai = 'servis_selesai';
}
