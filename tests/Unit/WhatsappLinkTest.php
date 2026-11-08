<?php

namespace Tests\Unit;

use App\Support\WhatsappLink;
use PHPUnit\Framework\TestCase;

class WhatsappLinkTest extends TestCase
{
    public function test_it_normalizes_indonesian_mobile_numbers(): void
    {
        $this->assertSame('6281234567890', WhatsappLink::normalize('081234567890'));
        $this->assertSame('6281234567890', WhatsappLink::normalize('+62 812-3456-7890'));
        $this->assertSame('6281234567890', WhatsappLink::normalize('6281234567890'));
        $this->assertSame('6281234567890', WhatsappLink::normalize('81234567890'));
    }

    public function test_it_rejects_empty_or_invalid_numbers(): void
    {
        $this->assertNull(WhatsappLink::normalize(null));
        $this->assertNull(WhatsappLink::normalize(''));
        $this->assertNull(WhatsappLink::normalize('abc'));
        $this->assertNull(WhatsappLink::normalize('021123456'));
        $this->assertNull(WhatsappLink::normalize('0812'));
    }

    public function test_it_builds_an_encoded_wa_link(): void
    {
        $link = WhatsappLink::for('081234567890', 'Halo Bapak/Ibu, jadwal 10:00 & bawa motor.');

        $this->assertSame(
            'https://wa.me/6281234567890?text=Halo%20Bapak%2FIbu%2C%20jadwal%2010%3A00%20%26%20bawa%20motor.',
            $link
        );
    }

    public function test_it_returns_null_for_invalid_numbers(): void
    {
        $this->assertNull(WhatsappLink::for(null, 'Halo'));
        $this->assertNull(WhatsappLink::for('12345', 'Halo'));
    }
}
