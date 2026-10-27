<?php

namespace Tests\Feature;

use App\Enums\StatusReservasi;
use App\Enums\UserRole;
use App\Models\Kendaraan;
use App\Models\Layanan;
use App\Models\MerkMotor;
use App\Models\Reservasi;
use App\Models\SlotWaktu;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class SlotConcurrencyTest extends TestCase
{
    use DatabaseTransactions;

    private User $firstCustomer;
    private User $secondCustomer;
    private MerkMotor $merk;
    private Layanan $layanan;
    private Kendaraan $firstVehicle;
    private Kendaraan $secondVehicle;
    private SlotWaktu $slot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->firstCustomer = $this->createCustomer('pertama');
        $this->secondCustomer = $this->createCustomer('kedua');

        $this->merk = MerkMotor::create([
            'nama' => 'Merk Konkuren '.Str::upper(Str::random(4)),
            'aktif' => true,
        ]);

        $this->layanan = Layanan::create([
            'nama' => 'Layanan Konkuren '.Str::upper(Str::random(4)),
            'deskripsi' => 'Layanan untuk pengujian konkurensi',
            'durasi_menit' => 60,
            'aktif' => true,
        ]);

        $this->firstVehicle = $this->createVehicle(
            $this->firstCustomer,
            'CC '.random_int(10000, 99999).' AA'
        );
        $this->secondVehicle = $this->createVehicle(
            $this->secondCustomer,
            'DD '.random_int(10000, 99999).' BB'
        );

        $this->slot = SlotWaktu::create([
            'tanggal' => today()->addDays(403),
            'jam_mulai' => '10:00',
            'jam_selesai' => '10:59',
            'kapasitas' => 1,
            'aktif' => true,
        ]);
    }

    public function test_last_slot_can_only_be_booked_once_when_two_customers_compete(): void
    {
        $this->actingAs($this->firstCustomer)
            ->post(route('reservasi.store'), $this->bookingPayload($this->firstVehicle))
            ->assertRedirect(route('booking-saya'));

        $this->assertSame(1, Reservasi::query()->where('id_slot', $this->slot->id)->count());

        $this->actingAs($this->secondCustomer)
            ->post(route('reservasi.store'), $this->bookingPayload($this->secondVehicle))
            ->assertSessionHasErrors('id_slot');

        $this->assertSame(1, Reservasi::query()->where('id_slot', $this->slot->id)->count());
        $this->assertDatabaseHas('reservasi', [
            'id_slot' => $this->slot->id,
            'user_id' => $this->firstCustomer->id,
            'kendaraan_id' => $this->firstVehicle->id,
            'status' => StatusReservasi::MenungguKonfirmasi->value,
        ]);
        $this->assertDatabaseMissing('reservasi', [
            'id_slot' => $this->slot->id,
            'user_id' => $this->secondCustomer->id,
        ]);
    }

    public function test_booking_locks_the_slot_row_for_update(): void
    {
        $queries = [];

        DB::listen(function ($query) use (&$queries) {
            $queries[] = Str::lower($query->sql);
        });

        $this->actingAs($this->firstCustomer)
            ->post(route('reservasi.store'), $this->bookingPayload($this->firstVehicle))
            ->assertRedirect(route('booking-saya'));

        $this->assertTrue(
            collect($queries)->contains(
                fn (string $sql) => str_contains($sql, 'slot_waktu')
                    && str_contains($sql, 'for update')
            ),
            'Query penguncian slot_waktu dengan lockForUpdate() tidak ditemukan.'
        );
    }

    public function test_check_in_reservation_counts_as_active_capacity(): void
    {
        Reservasi::create([
            'user_id' => $this->firstCustomer->id,
            'kendaraan_id' => $this->firstVehicle->id,
            'id_slot' => $this->slot->id,
            'kode' => 'RSV-CHECKIN-'.Str::upper(Str::random(8)),
            'status' => StatusReservasi::CheckIn,
        ]);

        $this->actingAs($this->secondCustomer)
            ->post(route('reservasi.store'), $this->bookingPayload($this->secondVehicle))
            ->assertSessionHasErrors('id_slot');

        $this->assertSame(1, Reservasi::query()->where('id_slot', $this->slot->id)->count());
    }

    public function test_cancelled_and_completed_reservations_do_not_occupy_capacity(): void
    {
        foreach ([StatusReservasi::Dibatalkan, StatusReservasi::Selesai] as $status) {
            Reservasi::create([
                'user_id' => $this->firstCustomer->id,
                'kendaraan_id' => $this->firstVehicle->id,
                'id_slot' => $this->slot->id,
                'kode' => 'RSV-FREE-'.Str::upper(Str::random(8)),
                'status' => $status,
            ]);
        }

        $this->actingAs($this->secondCustomer)
            ->post(route('reservasi.store'), $this->bookingPayload($this->secondVehicle))
            ->assertRedirect(route('booking-saya'));

        $this->assertDatabaseHas('reservasi', [
            'id_slot' => $this->slot->id,
            'user_id' => $this->secondCustomer->id,
            'status' => StatusReservasi::MenungguKonfirmasi->value,
        ]);
    }

    private function createCustomer(string $prefix): User
    {
        return User::create([
            'name' => 'Pelanggan '.ucfirst($prefix),
            'no_hp' => '0813'.random_int(10000000, 99999999),
            'email' => 'konkuren-'.$prefix.'-'.Str::lower(Str::random(8)).'@example.test',
            'password' => 'password',
            'role' => UserRole::Pelanggan,
            'aktif' => true,
        ]);
    }

    private function createVehicle(User $user, string $plate): Kendaraan
    {
        return Kendaraan::create([
            'user_id' => $user->id,
            'merk_motor_id' => $this->merk->id,
            'plat_nomor' => $plate,
            'model' => 'Motor Konkuren',
            'tahun' => 2024,
            'kilometer' => 0,
        ]);
    }

    private function bookingPayload(Kendaraan $kendaraan): array
    {
        return [
            'kendaraan_id' => $kendaraan->id,
            'id_slot' => $this->slot->id,
            'layanan' => [$this->layanan->id],
            'keluhan' => 'Uji kapasitas slot terakhir.',
        ];
    }
}
