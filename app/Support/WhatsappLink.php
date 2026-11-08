<?php

namespace App\Support;

final class WhatsappLink
{
    public static function normalize(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        return preg_match('/^628\d{7,11}$/', $digits) ? $digits : null;
    }

    public static function for(?string $phone, string $message): ?string
    {
        $normalized = self::normalize($phone);

        return $normalized === null
            ? null
            : 'https://wa.me/'.$normalized.'?text='.rawurlencode($message);
    }
}
