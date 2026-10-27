<?php

namespace App\Enums;

enum JenisWhatsapp: string
{
    case ReservasiBaru = 'reservasi_baru';
    case KonfirmasiReservasi = 'konfirmasi_reservasi';
    case PembatalanReservasi = 'pembatalan_reservasi';
    case PenolakanReservasi = 'penolakan_reservasi';
    case PengingatServis = 'pengingat_servis';
    case ServisSelesai = 'servis_selesai';

    public function label(): string
    {
        return match ($this) {
            self::ReservasiBaru => 'Reservasi Baru',
            self::KonfirmasiReservasi => 'Konfirmasi Reservasi',
            self::PembatalanReservasi => 'Pembatalan Reservasi',
            self::PenolakanReservasi => 'Penolakan Reservasi',
            self::PengingatServis => 'Pengingat Servis',
            self::ServisSelesai => 'Servis Selesai',
        };
    }
}
