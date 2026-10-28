<?php

namespace Tests\Feature;

use App\Enums\StatusReservasi;
use App\Enums\StatusServis;
use App\Enums\UserRole;
use App\Models\Kendaraan;
use App\Models\Layanan;
use App\Models\MerkMotor;
use App\Models\PengingatServis;
use App\Models\Reservasi;
use App\Models\Servis;
use App\Models\SlotWaktu;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class ServiceFlowTest extends TestCase
{
    use DatabaseTransactions;

    private User $admin;

    private User $customer;

    private MerkMotor $merk;

    private Layanan $layanan;

    private SlotWaktu $slot;

    private Kendaraan $kendaraan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Servis',
            'email' => 'admin-servis-'.Str::lower(Str::random(8)).'@example.test',
            'password' => 'password',
            'role' => UserRole::Admin,
            'aktif' => true,
        ]);

        $this->customer = User::create([
            'name' => 'Pelanggan Servis',
            'no_hp' => '081234567891',
            'email' => 'pelanggan-servis-'.Str::lower(Str::random(8)).'@example.test',
            'password' => 'password',
            'role' => UserRole::Pelanggan,
            'aktif' => true,
        ]);

        $this->merk = MerkMotor::create([
            'nama' => 'Merk Servis '.Str::upper(Str::random(4)),
            'aktif' => true,
        ]);

        $this->layanan = Layanan::create([
            'nama' => 'Servis Berkala '.Str::upper(Str::random(4)),
            'deskripsi' => 'Layanan untuk pengujian alur servis',
            'durasi_menit' => 60,
            'interval_servis_hari' => 180,
            'interval_servis_km' => 2000,
            'aktif' => true,
        ]);

        $this->slot = SlotWaktu::create([
            'tanggal' => today()->addDays(30),
            'jam_mulai' => '09:00',
            'jam_selesai' => '09:59',
            'kapasitas' => 2,
            'aktif' => true,
        ]);

        $this->kendaraan = Kendaraan::create([
            'user_id' => $this->customer->id,
            'merk_motor_id' => $this->merk->id,
            'plat_nomor' => 'ZZ '.Str::upper(Str::random(5)),
            'model' => 'Motor Servis',
            'tahun' => 2024,
            'kilometer' => 1000,
            'odometer_terakhir' => 1000,
        ]);
    }

    public function test_admin_check_in_creates_waiting_service_and_updates_reservation(): void
    {
        $reservasi = $this->createReservation(StatusReservasi::Dikonfirmasi);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.reservasi.check-in', $reservasi), [
                'odometer_awal' => 1234,
            ]);

        $response->assertRedirect(route('admin.servis.index'));
        $response->assertSessionHas('status');

        $this->assertSame(StatusReservasi::CheckIn, $reservasi->fresh()->status);

        $servis = Servis::query()->where('reservasi_id', $reservasi->id)->firstOrFail();

        $this->assertSame(StatusServis::Menunggu, $servis->status);
        $this->assertSame(1234, $servis->odometer_awal);
        $this->assertSame($this->kendaraan->id, $servis->kendaraan_id);
        $this->assertSame(today()->toDateString(), $servis->tanggal_masuk->toDateString());
    }

    public function test_check_in_requires_odometer_and_confirmed_reservation(): void
    {
        $pending = $this->createReservation();

        $this->actingAs($this->admin)
            ->patch(route('admin.reservasi.check-in', $pending), [
                'odometer_awal' => 1200,
            ])
            ->assertSessionHasErrors('reservasi');

        $this->assertSame(StatusReservasi::MenungguKonfirmasi, $pending->fresh()->status);
        $this->assertDatabaseMissing('servis', ['reservasi_id' => $pending->id]);

        $confirmed = $this->createReservation(StatusReservasi::Dikonfirmasi);

        $this->actingAs($this->admin)
            ->patch(route('admin.reservasi.check-in', $confirmed), [])
            ->assertSessionHasErrors('odometer_awal');

        $this->assertSame(StatusReservasi::Dikonfirmasi, $confirmed->fresh()->status);
        $this->assertDatabaseMissing('servis', ['reservasi_id' => $confirmed->id]);
    }

    public function test_reservation_cannot_be_checked_in_twice(): void
    {
        $reservasi = $this->createReservation(StatusReservasi::Dikonfirmasi);

        $this->actingAs($this->admin)
            ->patch(route('admin.reservasi.check-in', $reservasi), ['odometer_awal' => 1200])
            ->assertRedirect();

        $this->actingAs($this->admin)
            ->patch(route('admin.reservasi.check-in', $reservasi), ['odometer_awal' => 1300])
            ->assertSessionHasErrors('reservasi');

        $this->assertSame(1, Servis::query()->where('reservasi_id', $reservasi->id)->count());
        $this->assertSame(StatusReservasi::CheckIn, $reservasi->fresh()->status);
    }

    public function test_service_status_must_follow_the_exact_order_and_pickup_completes_reservation(): void
    {
        $reservasi = $this->createReservation(StatusReservasi::Dikonfirmasi);

        $this->actingAs($this->admin)
            ->patch(route('admin.reservasi.check-in', $reservasi), ['odometer_awal' => 1500])
            ->assertRedirect();

        $servis = Servis::query()->where('reservasi_id', $reservasi->id)->firstOrFail();

        $this->actingAs($this->admin)
            ->patch(route('admin.servis.status', $servis), ['status' => 'selesai'])
            ->assertSessionHasErrors('status');

        $this->assertSame(StatusServis::Menunggu, $servis->fresh()->status);
        $this->assertNull($servis->fresh()->waktu_selesai);

        $this->actingAs($this->admin)
            ->patch(route('admin.servis.status', $servis), ['status' => 'dikerjakan'])
            ->assertRedirect();

        $servis->refresh();
        $this->assertSame(StatusServis::Dikerjakan, $servis->status);
        $this->assertNotNull($servis->waktu_mulai);

        $this->actingAs($this->admin)
            ->patch(route('admin.servis.status', $servis), ['status' => 'menunggu'])
            ->assertSessionHasErrors('status');

        $this->assertSame(StatusServis::Dikerjakan, $servis->fresh()->status);

        $this->actingAs($this->admin)
            ->patch(route('admin.servis.update', $servis), [
                'odometer' => 1600,
                'rincian_pekerjaan' => 'Ganti oli, ganti kampas rem depan',
                'catatan' => 'Gunakan oli 10W-30.',
            ])
            ->assertRedirect();

        $servis->refresh();
        $this->assertSame(1600, $servis->odometer);
        $this->assertSame('Ganti oli, ganti kampas rem depan', $servis->rincian_pekerjaan);
        $this->assertSame('Gunakan oli 10W-30.', $servis->catatan);

        $this->actingAs($this->admin)
            ->patch(route('admin.servis.status', $servis), ['status' => 'selesai'])
            ->assertRedirect();

        $servis->refresh();
        $this->assertSame(StatusServis::Selesai, $servis->status);
        $this->assertNotNull($servis->waktu_selesai);
        $this->assertSame(today()->toDateString(), $servis->tanggal_selesai->toDateString());
        $this->assertDatabaseHas('whatsapp_logs', [
            'reservasi_id' => $reservasi->id,
            'jenis' => 'servis_selesai',
            'status' => 'menunggu',
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.servis.status', $servis), ['status' => 'diambil'])
            ->assertRedirect();

        $servis->refresh();
        $this->assertSame(StatusServis::Diambil, $servis->status);
        $this->assertSame(StatusReservasi::Selesai, $reservasi->fresh()->status);
        $this->assertSame(1600, $this->kendaraan->fresh()->odometer_terakhir);

        $pengingat = PengingatServis::query()
            ->where('servis_id', $servis->id)
            ->where('layanan_id', $this->layanan->id)
            ->firstOrFail();

        $this->assertSame(today()->addDays(180)->toDateString(), $pengingat->tanggal_berikutnya->toDateString());
        $this->assertSame(3600, $pengingat->kilometer_berikutnya);
    }

    public function test_admin_can_update_service_notes_but_not_after_pickup(): void
    {
        $reservasi = $this->createReservation(StatusReservasi::Dikonfirmasi);

        $this->actingAs($this->admin)
            ->patch(route('admin.reservasi.check-in', $reservasi), ['odometer_awal' => 2000])
            ->assertRedirect();

        $servis = Servis::query()->where('reservasi_id', $reservasi->id)->firstOrFail();

        $this->actingAs($this->admin)
            ->patch(route('admin.servis.update', $servis), [
                'odometer' => 2050,
                'rincian_pekerjaan' => 'Servis ringan dan pemeriksaan rem',
                'catatan' => 'Rem belakang perlu dipantau.',
                'total_biaya' => 999999,
            ])
            ->assertRedirect();

        $servis->refresh();
        $this->assertSame(2050, $servis->odometer);
        $this->assertSame('Servis ringan dan pemeriksaan rem', $servis->rincian_pekerjaan);
        $this->assertSame('Rem belakang perlu dipantau.', $servis->catatan);
        $this->assertSame('0.00', $servis->total_biaya);

        $servis->update(['status' => StatusServis::Diambil]);

        $this->actingAs($this->admin)
            ->patch(route('admin.servis.update', $servis), [
                'odometer' => 2100,
                'rincian_pekerjaan' => 'Perubahan setelah diambil',
            ])
            ->assertSessionHasErrors('servis');

        $servis->refresh();
        $this->assertSame(2050, $servis->odometer);
        $this->assertSame('Servis ringan dan pemeriksaan rem', $servis->rincian_pekerjaan);
    }

    public function test_admin_service_list_filters_by_status_and_date(): void
    {
        $reservasiHariIni = $this->createReservation(StatusReservasi::CheckIn, [
            'kode' => 'RSV-HARI-INI-'.Str::upper(Str::random(4)),
        ]);

        $servisHariIni = Servis::create([
            'reservasi_id' => $reservasiHariIni->id,
            'kendaraan_id' => $this->kendaraan->id,
            'tanggal_masuk' => today()->toDateString(),
            'status' => StatusServis::Menunggu,
            'odometer_awal' => 3000,
        ]);

        $kendaraanLain = Kendaraan::create([
            'user_id' => $this->customer->id,
            'merk_motor_id' => $this->merk->id,
            'plat_nomor' => 'ZZ '.Str::upper(Str::random(5)),
            'model' => 'Motor Lain',
            'tahun' => 2023,
            'kilometer' => 5000,
            'odometer_terakhir' => 5000,
        ]);

        $reservasiLama = $this->createReservation(StatusReservasi::CheckIn, [
            'kode' => 'RSV-KEMARIN-'.Str::upper(Str::random(4)),
            'kendaraan_id' => $kendaraanLain->id,
        ]);

        Servis::create([
            'reservasi_id' => $reservasiLama->id,
            'kendaraan_id' => $kendaraanLain->id,
            'tanggal_masuk' => today()->subDay()->toDateString(),
            'status' => StatusServis::Dikerjakan,
            'odometer_awal' => 5000,
            'waktu_mulai' => now()->subDay(),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.servis.index', [
                'status' => 'menunggu',
                'dari' => today()->toDateString(),
                'sampai' => today()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee($reservasiHariIni->kode)
            ->assertSee($this->kendaraan->plat_nomor)
            ->assertDontSee($reservasiLama->kode)
            ->assertDontSee($kendaraanLain->plat_nomor)
            ->assertSee('Daftar Servis')
            ->assertSee('Menunggu');

        $this->assertSame(StatusServis::Menunggu, $servisHariIni->fresh()->status);
    }

    public function test_customer_sees_service_progress_and_vehicle_history(): void
    {
        $reservasi = $this->createReservation(StatusReservasi::CheckIn);
        $reservasi->update(['status' => StatusReservasi::CheckIn]);

        $servis = Servis::create([
            'reservasi_id' => $reservasi->id,
            'kendaraan_id' => $this->kendaraan->id,
            'tanggal_masuk' => today()->toDateString(),
            'status' => StatusServis::Dikerjakan,
            'odometer_awal' => 1500,
            'odometer' => 1600,
            'waktu_mulai' => now(),
            'rincian_pekerjaan' => 'Ganti oli, ganti kampas rem depan',
            'catatan' => 'Gunakan oli 10W-30.',
        ]);

        $this->actingAs($this->customer)
            ->get(route('booking-saya'))
            ->assertOk()
            ->assertSee('Progres Servis')
            ->assertSee('Dikerjakan')
            ->assertSee('langkah 2 dari 4');

        $this->actingAs($this->customer)
            ->get(route('riwayat-servis.index'))
            ->assertOk()
            ->assertSee('Riwayat Servis')
            ->assertSee($this->layanan->nama)
            ->assertSee('Ganti oli, ganti kampas rem depan')
            ->assertSee('Gunakan oli 10W-30.')
            ->assertSee('1.600 km')
            ->assertSee('Dikerjakan');

        $this->actingAs($this->customer)
            ->get(route('riwayat-servis.index', ['kendaraan' => $this->kendaraan->id]))
            ->assertOk()
            ->assertSee($this->kendaraan->plat_nomor)
            ->assertSee('Ganti oli, ganti kampas rem depan');

        $this->assertSame(StatusServis::Dikerjakan, $servis->fresh()->status);
    }

    public function test_customer_cannot_filter_history_by_another_customers_vehicle(): void
    {
        $other = User::create([
            'name' => 'Pelanggan Lain',
            'no_hp' => '081299999998',
            'email' => 'lain-servis-'.Str::lower(Str::random(8)).'@example.test',
            'password' => 'password',
            'role' => UserRole::Pelanggan,
            'aktif' => true,
        ]);

        $kendaraanLain = Kendaraan::create([
            'user_id' => $other->id,
            'merk_motor_id' => $this->merk->id,
            'plat_nomor' => 'ZZ '.Str::upper(Str::random(5)),
            'model' => 'Motor Orang Lain',
            'tahun' => 2022,
        ]);

        $this->actingAs($this->customer)
            ->get(route('riwayat-servis.index', ['kendaraan' => $kendaraanLain->id]))
            ->assertNotFound();
    }

    private function createReservation(StatusReservasi $status = StatusReservasi::MenungguKonfirmasi, array $attributes = []): Reservasi
    {
        $reservasi = Reservasi::create(array_merge([
            'user_id' => $this->customer->id,
            'kendaraan_id' => $this->kendaraan->id,
            'id_slot' => $this->slot->id,
            'kode' => 'RSV-SERVIS-'.Str::upper(Str::random(6)),
            'status' => $status,
            'keluhan' => 'Mesin kurang bertenaga',
        ], $attributes));

        $reservasi->layanan()->attach($this->layanan->id, ['jumlah' => 1]);

        return $reservasi;
    }
}
