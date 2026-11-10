<?php

namespace Tests\Feature;

use App\Enums\StatusReservasi;
use App\Enums\UserRole;
use App\Models\Kendaraan;
use App\Models\MerkMotor;
use App\Models\Reservasi;
use App\Models\SlotWaktu;
use App\Models\User;
use App\Services\SlotAvailabilityService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicSlotTest extends TestCase
{
    use DatabaseTransactions;

    private User $customer;
    private MerkMotor $merk;
    private Kendaraan $kendaraan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::create([
            'name' => 'Pelanggan Slot',
            'no_hp' => '081298765432',
            'email' => 'slot-'.Str::lower(Str::random(8)).'@example.test',
            'password' => 'password',
            'role' => UserRole::Pelanggan,
            'aktif' => true,
        ]);

        $this->merk = MerkMotor::create([
            'nama' => 'Merk Slot '.Str::upper(Str::random(4)),
            'aktif' => true,
        ]);

        $this->kendaraan = Kendaraan::create([
            'user_id' => $this->customer->id,
            'merk_motor_id' => $this->merk->id,
            'plat_nomor' => 'SL '.Str::upper(Str::random(5)),
            'model' => 'Motor Slot',
            'tahun' => 2024,
            'kilometer' => 0,
        ]);
    }

    public function test_public_slot_page_is_accessible_without_login(): void
    {
        $slot = $this->createSlot(today()->addDays(400), '08:00', 2);

        $this->get(route('slot.publik', ['tanggal' => $slot->tanggal->toDateString()]))
            ->assertOk()
            ->assertSee('Cek Slot Servis')
            ->assertSee('name="tanggal"', false)
            ->assertSee('Booking Slot Ini')
            ->assertSee('Biaya menyesuaikan merk dan kondisi motor, dikonfirmasi bengkel.')
            ->assertDontSee('id="layout-menu"', false);
    }

    public function test_customer_slot_page_uses_customer_layout_with_sidebar(): void
    {
        $slot = $this->createSlot(today()->addDays(404), '09:00', 2);

        $this->actingAs($this->customer)
            ->get(route('slot.publik', ['tanggal' => $slot->tanggal->toDateString()]))
            ->assertOk()
            ->assertSee('id="layout-menu"', false)
            ->assertSee('Booking Saya')
            ->assertSee('Kendaraan Saya')
            ->assertSee('Cek Slot Servis')
            ->assertSee('Booking Slot Ini')
            ->assertSee('scrollbar-width: none', false)
            ->assertSee('name="tanggal"', false);
    }

    public function test_public_slot_page_shows_capacity_and_availability_status(): void
    {
        $date = today()->addDays(401);

        $tersedia = $this->createSlot($date, '08:00', 4);
        $hampirPenuh = $this->createSlot($date, '09:00', 4);
        $penuh = $this->createSlot($date, '10:00', 2);
        $nonaktif = $this->createSlot($date, '11:00', 4, false);

        $this->createReservation($tersedia, StatusReservasi::MenungguKonfirmasi);
        $this->createReservation($tersedia, StatusReservasi::Selesai);
        $this->createReservation($tersedia, StatusReservasi::Dibatalkan);

        $this->createReservation($hampirPenuh, StatusReservasi::Dikonfirmasi);
        $this->createReservation($hampirPenuh, StatusReservasi::CheckIn);

        $this->createReservation($penuh, StatusReservasi::MenungguKonfirmasi);
        $this->createReservation($penuh, StatusReservasi::Dikonfirmasi);

        $response = $this->get(route('slot.publik', ['tanggal' => $date->toDateString()]));

        $response->assertOk()
            ->assertViewHas('date', fn ($viewDate) => $viewDate->toDateString() === $date->toDateString())
            ->assertViewHas('slots', function ($slots) use ($tersedia, $hampirPenuh, $penuh, $nonaktif) {
                $tersediaSlot = $slots->firstWhere('id', $tersedia->id);
                $hampirSlot = $slots->firstWhere('id', $hampirPenuh->id);
                $penuhSlot = $slots->firstWhere('id', $penuh->id);

                return $slots->count() === 3
                    && ! $slots->contains('id', $nonaktif->id)
                    && $tersediaSlot->terpakai === 1
                    && $tersediaSlot->sisa === 3
                    && $tersediaSlot->status_ketersediaan === SlotAvailabilityService::STATUS_TERSEDIA
                    && $hampirSlot->terpakai === 2
                    && $hampirSlot->sisa === 2
                    && $hampirSlot->status_ketersediaan === SlotAvailabilityService::STATUS_HAMPIR_PENUH
                    && $penuhSlot->terpakai === 2
                    && $penuhSlot->sisa === 0
                    && $penuhSlot->status_ketersediaan === SlotAvailabilityService::STATUS_PENUH;
            })
            ->assertSee('Tersedia: 1')
            ->assertSee('Hampir penuh: 1')
            ->assertSee('Penuh: 1')
            ->assertSee('Slot penuh')
            ->assertDontSee('11:00');
    }

    public function test_public_slot_page_defaults_to_today(): void
    {
        $slot = $this->createSlot(today(), '23:45', 2);

        $this->get(route('slot.publik'))
            ->assertOk()
            ->assertViewHas('date', fn ($date) => $date->toDateString() === today()->toDateString())
            ->assertSee('value="'.today()->toDateString().'"', false)
            ->assertSee('23:45');
    }

    public function test_public_slot_page_rejects_invalid_date(): void
    {
        $this->get(route('slot.publik', ['tanggal' => '2026-02-30']))
            ->assertOk()
            ->assertSee('Format tanggal tidak valid. Gunakan format YYYY-MM-DD.')
            ->assertViewHas('date', fn ($date) => $date->toDateString() === today()->toDateString());

        $this->get(route('slot.publik', ['tanggal' => '30-02-2026']))
            ->assertOk()
            ->assertSee('Format tanggal tidak valid. Gunakan format YYYY-MM-DD.');
    }

    public function test_capacity_one_slot_is_never_marked_almost_full(): void
    {
        $service = app(SlotAvailabilityService::class);

        $this->assertSame(SlotAvailabilityService::STATUS_TERSEDIA, $service->status(1, 0));
        $this->assertSame(SlotAvailabilityService::STATUS_PENUH, $service->status(1, 1));
        $this->assertSame(SlotAvailabilityService::STATUS_TERSEDIA, $service->status(4, 1));
        $this->assertSame(SlotAvailabilityService::STATUS_HAMPIR_PENUH, $service->status(4, 2));
        $this->assertSame(SlotAvailabilityService::STATUS_PENUH, $service->status(4, 4));
    }

    public function test_guest_is_returned_to_the_selected_slot_after_login(): void
    {
        $slot = $this->createSlot(today()->addDays(402), '10:00', 2);
        $intended = route('booking-saya', [
            'slot' => $slot->id,
            'tanggal' => $slot->tanggal->toDateString(),
        ]);

        $this->get($intended)->assertRedirect(route('login'));

        $this->assertSame($intended, session('url.intended'));

        $this->post(route('login'), [
            'email' => $this->customer->email,
            'password' => 'password',
        ])->assertRedirect($intended);

        $this->get($intended)
            ->assertOk()
            ->assertViewHas('requestedSlotId', $slot->id)
            ->assertSee('modalBuatReservasi', false)
            ->assertSee('value="'.$slot->id.'"', false);
    }

    private function createSlot(
        $date,
        string $time,
        int $capacity = 2,
        bool $active = true,
    ): SlotWaktu {
        return SlotWaktu::create([
            'tanggal' => $date,
            'jam_mulai' => $time,
            'jam_selesai' => substr($time, 0, 2).':59',
            'kapasitas' => $capacity,
            'aktif' => $active,
        ]);
    }

    private function createReservation(SlotWaktu $slot, StatusReservasi $status): Reservasi
    {
        return Reservasi::create([
            'user_id' => $this->customer->id,
            'kendaraan_id' => $this->kendaraan->id,
            'id_slot' => $slot->id,
            'kode' => 'RSV-SLOT-'.Str::upper(Str::random(8)),
            'status' => $status,
            'keluhan' => null,
        ]);
    }
}
