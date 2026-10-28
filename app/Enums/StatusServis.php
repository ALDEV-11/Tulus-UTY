<?php

namespace App\Enums;

enum StatusServis: string
{
    case Menunggu = 'menunggu';
    case Dikerjakan = 'dikerjakan';
    case Selesai = 'selesai';
    case Diambil = 'diambil';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu',
            self::Dikerjakan => 'Dikerjakan',
            self::Selesai => 'Selesai',
            self::Diambil => 'Diambil',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Menunggu => 'warning',
            self::Dikerjakan => 'info',
            self::Selesai => 'success',
            self::Diambil => 'primary',
        };
    }

    public function urutan(): int
    {
        return match ($this) {
            self::Menunggu => 1,
            self::Dikerjakan => 2,
            self::Selesai => 3,
            self::Diambil => 4,
        };
    }

    public function berikutnya(): ?self
    {
        return match ($this) {
            self::Menunggu => self::Dikerjakan,
            self::Dikerjakan => self::Selesai,
            self::Selesai => self::Diambil,
            self::Diambil => null,
        };
    }

    public function bisaBerpindahKe(self $status): bool
    {
        return $this->berikutnya() === $status;
    }

    public static function urutanLangkah(): array
    {
        return [
            self::Menunggu->value,
            self::Dikerjakan->value,
            self::Selesai->value,
            self::Diambil->value,
        ];
    }
}
