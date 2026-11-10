<?php

namespace Tests\Feature;

use App\Enums\StatusReservasi;
use App\Enums\UserRole;
use App\Models\Kendaraan;
use App\Models\MerkMotor;
use App\Models\Reservasi;
use App\Models\SlotWaktu;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class KendaraanTest extends TestCase
{
    use DatabaseTransactions;

    private User $customer;
    private User $otherCustomer;
    private User $admin;
    private MerkMotor $merk;
    private MerkMotor $inactiveMerk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = $this->createUser(UserRole::Pelanggan, 'pemilik');
        $this->otherCustomer = $this->createUser(UserRole::Pelanggan, 'lain');
        $this->admin = $this->createUser(UserRole::Admin, 'admin');

        $this->merk = MerkMotor::create([
            'nama' => 'Honda '.Str::upper(Str::random(4)),
            'aktif' => true,
        ]);

        $this->inactiveMerk = MerkMotor::create([
            'nama' => 'Merk Nonaktif '.Str::upper(Str::random(4)),
            'aktif' => false,
        ]);
    }

    public function test_customer_can_create_vehicle_with_normalized_plate_number(): void
    {
        $plate = $this->uniquePlate();

        $response = $this->actingAs($this->customer)->post(route('kendaraan.store'), [
            'merk_motor_id' => $this->merk->id,
            'plat_nomor' => '  '.Str::lower($plate).'   ',
            'model' => 'Beat Street',
            'tahun' => 2023,
        ]);

        $response->assertRedirect(route('kendaraan.index'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('kendaraan', [
            'user_id' => $this->customer->id,
            'merk_motor_id' => $this->merk->id,
            'plat_nomor' => Kendaraan::normalizePlatNomor($plate),
            'model' => 'Beat Street',
            'tahun' => 2023,
        ]);
    }

    public function test_customer_can_update_own_vehicle(): void
    {
        $kendaraan = $this->createVehicle($this->customer, $this->uniquePlate());
        $newPlate = $this->uniquePlate();

        $response = $this->actingAs($this->customer)->put(route('kendaraan.update', $kendaraan), [
            'merk_motor_id' => $this->merk->id,
            'plat_nomor' => Str::lower($newPlate),
            'model' => 'Vario 160',
            'tahun' => 2024,
        ]);

        $response->assertRedirect(route('kendaraan.index'));

        $kendaraan->refresh();

        $this->assertSame(Kendaraan::normalizePlatNomor($newPlate), $kendaraan->plat_nomor);
        $this->assertSame('Vario 160', $kendaraan->model);
        $this->assertSame(2024, $kendaraan->tahun);
    }

    public function test_customer_can_open_create_and_edit_vehicle_pages(): void
    {
        $kendaraan = $this->createVehicle($this->customer, $this->uniquePlate());

        $this->actingAs($this->customer)
            ->get(route('kendaraan.create'))
            ->assertOk()
            ->assertSee('Tambah Kendaraan')
            ->assertSee($this->merk->nama)
            ->assertSee('Kendaraan Saya');

        $this->actingAs($this->customer)
            ->get(route('kendaraan.edit', $kendaraan))
            ->assertOk()
            ->assertSee('Edit Kendaraan')
            ->assertSee($kendaraan->plat_nomor);
    }

    public function test_duplicate_plate_number_is_rejected_after_normalization(): void
    {
        $plate = $this->uniquePlate();
        $this->createVehicle($this->customer, $plate);

        $response = $this->actingAs($this->customer)->post(route('kendaraan.store'), [
            'merk_motor_id' => $this->merk->id,
            'plat_nomor' => Str::lower($plate),
            'model' => 'Motor Baru',
            'tahun' => 2024,
        ]);

        $response->assertSessionHasErrors('plat_nomor');
        $this->assertSame(1, Kendaraan::query()->where('plat_nomor', $plate)->count());
    }

    public function test_inactive_merk_cannot_be_used_for_a_vehicle(): void
    {
        $plate = $this->uniquePlate();

        $response = $this->actingAs($this->customer)->post(route('kendaraan.store'), [
            'merk_motor_id' => $this->inactiveMerk->id,
            'plat_nomor' => $plate,
            'model' => 'Motor Nonaktif',
            'tahun' => 2024,
        ]);

        $response->assertSessionHasErrors('merk_motor_id');
        $this->assertDatabaseMissing('kendaraan', ['plat_nomor' => $plate]);
    }

    public function test_customer_only_sees_own_vehicles(): void
    {
        $ownPlate = $this->uniquePlate();
        $otherPlate = $this->uniquePlate();

        $this->createVehicle($this->customer, $ownPlate);
        $this->createVehicle($this->otherCustomer, $otherPlate);

        $this->actingAs($this->customer)
            ->get(route('kendaraan.index'))
            ->assertOk()
            ->assertSee($ownPlate)
            ->assertDontSee($otherPlate);
    }

    public function test_customer_cannot_edit_or_delete_another_customers_vehicle(): void
    {
        $plate = $this->uniquePlate();
        $kendaraan = $this->createVehicle($this->otherCustomer, $plate);

        $this->actingAs($this->customer)
            ->get(route('kendaraan.edit', $kendaraan))
            ->assertForbidden();

        $this->actingAs($this->customer)
            ->put(route('kendaraan.update', $kendaraan), [
                'merk_motor_id' => $this->merk->id,
                'plat_nomor' => $this->uniquePlate(),
                'model' => 'Diubah',
                'tahun' => 2024,
            ])
            ->assertForbidden();

        $this->actingAs($this->customer)
            ->delete(route('kendaraan.destroy', $kendaraan))
            ->assertForbidden();

        $this->assertDatabaseHas('kendaraan', [
            'id' => $kendaraan->id,
            'plat_nomor' => $plate,
        ]);
    }

    public function test_vehicle_with_reservation_history_cannot_be_deleted(): void
    {
        $kendaraan = $this->createVehicle($this->customer, $this->uniquePlate());
        $slot = SlotWaktu::create([
            'tanggal' => today()->addDays(30),
            'jam_mulai' => '09:00',
            'jam_selesai' => '09:59',
            'kapasitas' => 2,
            'aktif' => true,
        ]);

        Reservasi::create([
            'user_id' => $this->customer->id,
            'kendaraan_id' => $kendaraan->id,
            'id_slot' => $slot->id,
            'kode' => 'RSV-KENDARAAN-'.Str::upper(Str::random(8)),
            'status' => StatusReservasi::Dibatalkan,
        ]);

        $response = $this->actingAs($this->customer)->delete(route('kendaraan.destroy', $kendaraan));

        $response->assertRedirect();
        $response->assertSessionHasErrors('kendaraan');
        $this->assertDatabaseHas('kendaraan', ['id' => $kendaraan->id]);
    }

    public function test_customer_can_delete_vehicle_without_history(): void
    {
        $kendaraan = $this->createVehicle($this->customer, $this->uniquePlate());

        $response = $this->actingAs($this->customer)->delete(route('kendaraan.destroy', $kendaraan));

        $response->assertRedirect(route('kendaraan.index'));
        $this->assertDatabaseMissing('kendaraan', ['id' => $kendaraan->id]);
    }

    public function test_admin_cannot_access_customer_vehicle_routes(): void
    {
        $this->actingAs($this->admin)
            ->get(route('kendaraan.index'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->post(route('kendaraan.store'), [
                'merk_motor_id' => $this->merk->id,
                'plat_nomor' => $this->uniquePlate(),
                'model' => 'Motor Admin',
                'tahun' => 2024,
            ])
            ->assertForbidden();
    }

    private function createUser(UserRole $role, string $prefix): User
    {
        return User::create([
            'name' => ucfirst($prefix).' Test',
            'no_hp' => $role === UserRole::Pelanggan ? '0812'.random_int(10000000, 99999999) : null,
            'email' => $prefix.'-'.Str::lower(Str::random(8)).'@example.test',
            'password' => 'password',
            'role' => $role,
            'aktif' => true,
        ]);
    }

    private function uniquePlate(): string
    {
        return 'ZZ '.random_int(10000, 99999).' '.Str::upper(Str::random(2));
    }

    private function createVehicle(User $user, string $plate): Kendaraan
    {
        return Kendaraan::create([
            'user_id' => $user->id,
            'merk_motor_id' => $this->merk->id,
            'plat_nomor' => $plate,
            'model' => 'Motor Test',
            'tahun' => 2024,
            'kilometer' => 0,
        ]);
    }
}
