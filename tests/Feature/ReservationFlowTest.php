<?php

namespace Tests\Feature;

use App\Enums\StatusReservasi;
use App\Enums\StatusServis;
use App\Enums\UserRole;
use App\Events\ReservationCreated;
use App\Models\Kendaraan;
use App\Models\Layanan;
use App\Models\MerkMotor;
use App\Models\Reservasi;
use App\Models\Servis;
use App\Models\SlotWaktu;
use App\Models\User;
use App\Models\WhatsappLog;
use App\Services\WhatsappLogService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReservationFlowTest extends TestCase
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
            'name' => 'Admin Test',
            'email' => 'admin-'.Str::lower(Str::random(8)).'@example.test',
            'password' => 'password',
            'role' => UserRole::Admin,
            'aktif' => true,
        ]);

        $this->customer = User::create([
            'name' => 'Pelanggan Test',
            'no_hp' => '081234567890',
            'email' => 'pelanggan-'.Str::lower(Str::random(8)).'@example.test',
            'password' => 'password',
            'role' => UserRole::Pelanggan,
            'aktif' => true,
        ]);

        $this->merk = MerkMotor::create([
            'nama' => 'Merk Test '.Str::upper(Str::random(4)),
            'aktif' => true,
        ]);

        $this->layanan = Layanan::create([
            'nama' => 'Servis Test '.Str::upper(Str::random(4)),
            'deskripsi' => 'Layanan untuk pengujian',
            'durasi_menit' => 60,
            'aktif' => true,
        ]);

        $this->slot = SlotWaktu::create([
            'tanggal' => today()->addDays(60),
            'jam_mulai' => '23:00',
            'jam_selesai' => '23:59',
            'kapasitas' => 1,
            'aktif' => true,
        ]);

        $this->kendaraan = Kendaraan::create([
            'user_id' => $this->customer->id,
            'merk_motor_id' => $this->merk->id,
            'plat_nomor' => 'ZZ '.Str::upper(Str::random(5)),
            'model' => 'Model Test',
            'tahun' => 2024,
            'kilometer' => 1000,
        ]);
    }

    public function test_customer_can_create_reservation_and_whatsapp_log_is_created(): void
    {
        Event::fake([ReservationCreated::class]);

        $response = $this->actingAs($this->customer)->post(route('reservasi.store'), [
            'kendaraan_id' => $this->kendaraan->id,
            'id_slot' => $this->slot->id,
            'layanan' => [$this->layanan->id],
            'keluhan' => 'Motor berbunyi kasar.',
        ]);

        $response->assertRedirect(route('booking-saya'));
        $response->assertSessionHas('status');

        $reservasi = Reservasi::query()->where('user_id', $this->customer->id)->latest()->firstOrFail();

        $this->assertSame(StatusReservasi::MenungguKonfirmasi, $reservasi->status);
        $this->assertDatabaseHas('reservasi_layanan', [
            'reservasi_id' => $reservasi->id,
            'layanan_id' => $this->layanan->id,
        ]);
        $this->assertDatabaseHas('whatsapp_logs', [
            'reservasi_id' => $reservasi->id,
            'jenis' => 'reservasi_baru',
            'nomor_tujuan' => '6281234567890',
            'status' => 'menunggu',
        ]);

        Event::assertDispatched(ReservationCreated::class);
    }

    public function test_customer_cannot_book_a_full_slot(): void
    {
        $this->createReservation();

        $response = $this->actingAs($this->customer)->post(route('reservasi.store'), [
            'kendaraan_id' => $this->kendaraan->id,
            'id_slot' => $this->slot->id,
            'layanan' => [$this->layanan->id],
        ]);

        $response->assertSessionHasErrors('id_slot');
        $this->assertSame(1, Reservasi::query()->where('id_slot', $this->slot->id)->count());
    }

    public function test_customer_can_cancel_own_reservation_and_whatsapp_log_is_created(): void
    {
        $reservasi = $this->createReservation();

        $response = $this->actingAs($this->customer)->post(route('reservasi.cancel', $reservasi));

        $response->assertRedirect();
        $this->assertSame(StatusReservasi::Dibatalkan, $reservasi->fresh()->status);
        $this->assertDatabaseHas('whatsapp_logs', [
            'reservasi_id' => $reservasi->id,
            'jenis' => 'pembatalan_reservasi',
            'status' => 'menunggu',
        ]);
    }

    public function test_admin_can_confirm_reservation_and_whatsapp_log_is_created(): void
    {
        $reservasi = $this->createReservation();

        $response = $this->actingAs($this->admin)->patch(route('admin.reservasi.konfirmasi', $reservasi));

        $response->assertRedirect();
        $this->assertSame(StatusReservasi::Dikonfirmasi, $reservasi->fresh()->status);
        $this->assertDatabaseHas('whatsapp_logs', [
            'reservasi_id' => $reservasi->id,
            'jenis' => 'konfirmasi_reservasi',
            'status' => 'menunggu',
        ]);
    }

    public function test_admin_can_reject_pending_reservation_with_reason(): void
    {
        $reservasi = $this->createReservation();

        $response = $this->actingAs($this->admin)->patch(route('admin.reservasi.batalkan', $reservasi), [
            'alasan' => 'Jadwal penuh, silakan pilih hari lain.',
        ]);

        $response->assertRedirect();

        $reservasi->refresh();

        $this->assertSame(StatusReservasi::Dibatalkan, $reservasi->status);
        $this->assertStringContainsString('Ditolak admin: Jadwal penuh, silakan pilih hari lain.', $reservasi->catatan);

        $log = WhatsappLog::query()
            ->where('reservasi_id', $reservasi->id)
            ->where('jenis', 'penolakan_reservasi')
            ->firstOrFail();

        $this->assertSame('menunggu', $log->status);
        $this->assertStringContainsString('Jadwal penuh, silakan pilih hari lain.', $log->pesan);
        $this->assertStringContainsString('Silakan pilih jadwal lain', $log->pesan);
    }

    public function test_admin_must_fill_reason_when_rejecting_pending_reservation(): void
    {
        $reservasi = $this->createReservation();

        $response = $this->actingAs($this->admin)->patch(route('admin.reservasi.batalkan', $reservasi), []);

        $response->assertSessionHasErrors('alasan');
        $this->assertSame(StatusReservasi::MenungguKonfirmasi, $reservasi->fresh()->status);
        $this->assertDatabaseMissing('whatsapp_logs', [
            'reservasi_id' => $reservasi->id,
            'jenis' => 'penolakan_reservasi',
        ]);
    }

    public function test_admin_can_cancel_confirmed_reservation_without_reason(): void
    {
        $reservasi = $this->createReservation(['status' => StatusReservasi::Dikonfirmasi]);

        $response = $this->actingAs($this->admin)->patch(route('admin.reservasi.batalkan', $reservasi));

        $response->assertRedirect();
        $this->assertSame(StatusReservasi::Dibatalkan, $reservasi->fresh()->status);
        $this->assertDatabaseHas('whatsapp_logs', [
            'reservasi_id' => $reservasi->id,
            'jenis' => 'pembatalan_reservasi',
            'status' => 'menunggu',
        ]);
    }

    public function test_admin_can_complete_confirmed_reservation_and_service_is_created(): void
    {
        $reservasi = $this->createReservation();

        $this->actingAs($this->admin)
            ->patch(route('admin.reservasi.konfirmasi', $reservasi))
            ->assertRedirect();

        $response = $this->actingAs($this->admin)->patch(route('admin.reservasi.selesaikan', $reservasi), [
            'tanggal_selesai' => $this->slot->tanggal->toDateString(),
            'mekanik_id' => $this->admin->id,
            'diagnosa' => 'Oli mesin kotor',
            'tindakan' => 'Ganti oli mesin',
            'total_biaya' => 150000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $reservasi->refresh();

        $this->assertSame(StatusReservasi::Selesai, $reservasi->status);

        $servis = Servis::query()->where('reservasi_id', $reservasi->id)->firstOrFail();

        $this->assertSame(StatusServis::Selesai, $servis->status);
        $this->assertSame($this->slot->tanggal->toDateString(), $servis->tanggal_masuk->toDateString());
        $this->assertSame($this->slot->tanggal->toDateString(), $servis->tanggal_selesai->toDateString());
        $this->assertSame($this->admin->id, $servis->mekanik_id);
        $this->assertSame('Ganti oli mesin', $servis->tindakan);
        $this->assertSame('150000.00', $servis->total_biaya);

        $this->assertDatabaseHas('whatsapp_logs', [
            'reservasi_id' => $reservasi->id,
            'jenis' => 'servis_selesai',
            'status' => 'menunggu',
        ]);
    }

    public function test_admin_cannot_complete_reservation_before_confirmation(): void
    {
        $reservasi = $this->createReservation();

        $response = $this->actingAs($this->admin)->patch(route('admin.reservasi.selesaikan', $reservasi), [
            'tanggal_selesai' => $this->slot->tanggal->toDateString(),
            'total_biaya' => 100000,
        ]);

        $response->assertSessionHasErrors('reservasi');
        $this->assertSame(StatusReservasi::MenungguKonfirmasi, $reservasi->fresh()->status);
        $this->assertDatabaseMissing('servis', ['reservasi_id' => $reservasi->id]);
    }

    public function test_admin_can_complete_existing_service(): void
    {
        $reservasi = $this->createReservation(['status' => StatusReservasi::Dikonfirmasi]);

        $servis = Servis::create([
            'reservasi_id' => $reservasi->id,
            'kendaraan_id' => $this->kendaraan->id,
            'tanggal_masuk' => $this->slot->tanggal->toDateString(),
            'status' => StatusServis::Dikerjakan,
            'diagnosa' => 'Diagnosa awal',
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.reservasi.selesaikan', $reservasi), [
            'tanggal_selesai' => $this->slot->tanggal->toDateString(),
            'diagnosa' => 'Diagnosa akhir',
            'tindakan' => 'Servis selesai',
            'total_biaya' => 200000,
        ]);

        $response->assertRedirect();

        $servis->refresh();

        $this->assertSame(StatusServis::Selesai, $servis->status);
        $this->assertSame('Diagnosa akhir', $servis->diagnosa);
        $this->assertSame('Servis selesai', $servis->tindakan);
        $this->assertSame('200000.00', $servis->total_biaya);
        $this->assertSame(1, Servis::query()->where('reservasi_id', $reservasi->id)->count());
        $this->assertSame(StatusReservasi::Selesai, $reservasi->fresh()->status);
    }

    public function test_admin_cannot_complete_reservation_twice(): void
    {
        $reservasi = $this->createReservation(['status' => StatusReservasi::Dikonfirmasi]);
        $payload = [
            'tanggal_selesai' => $this->slot->tanggal->toDateString(),
            'total_biaya' => 100000,
        ];

        $this->actingAs($this->admin)
            ->patch(route('admin.reservasi.selesaikan', $reservasi), $payload)
            ->assertRedirect();

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.reservasi.selesaikan', $reservasi), $payload);

        $response->assertSessionHasErrors('reservasi');
        $this->assertSame(1, Servis::query()->where('reservasi_id', $reservasi->id)->count());
        $this->assertSame(1, WhatsappLog::query()
            ->where('reservasi_id', $reservasi->id)
            ->where('jenis', 'servis_selesai')
            ->count());
    }

    public function test_admin_reservation_page_shows_finish_service_form_for_confirmed_reservation(): void
    {
        $reservasi = $this->createReservation(['status' => StatusReservasi::Dikonfirmasi]);

        $this->actingAs($this->admin)
            ->get(route('admin.reservasi.index'))
            ->assertOk()
            ->assertSee('Selesaikan Servis')
            ->assertSee('name="tanggal_selesai"', false)
            ->assertSee('form-selesaikan-servis-'.$reservasi->id, false);
    }

    public function test_admin_reservation_page_and_notification_endpoint_work(): void
    {
        $reservasi = $this->createReservation();
        app(WhatsappLogService::class)->reservationCreated($reservasi);

        $this->actingAs($this->admin)
            ->get(route('admin.reservasi.index'))
            ->assertOk()
            ->assertSee('Daftar Reservasi')
            ->assertSee('Log WhatsApp');

        $this->actingAs($this->admin)
            ->getJson(route('admin.reservasi.notifications'))
            ->assertOk()
            ->assertJsonStructure(['count', 'items'])
            ->assertJsonFragment(['kode' => $reservasi->kode]);
    }

    public function test_admin_page_loads_pusher_script_when_configured(): void
    {
        config([
            'broadcasting.default' => 'pusher',
            'broadcasting.connections.pusher.key' => 'test-key',
            'broadcasting.connections.pusher.secret' => 'test-secret',
            'broadcasting.connections.pusher.app_id' => 'test-app',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.reservasi.index'))
            ->assertOk()
            ->assertSee('js.pusher.com/8.4.0/pusher.min.js', false)
            ->assertSee('private-admin.reservations', false);
    }

    public function test_customer_dashboard_and_whatsapp_log_page_load(): void
    {
        $reservasi = $this->createReservation();
        app(WhatsappLogService::class)->reservationCreated($reservasi);

        $this->actingAs($this->customer)
            ->get(route('booking-saya'))
            ->assertOk()
            ->assertSee('Buat Reservasi')
            ->assertSee($reservasi->kode);

        $this->actingAs($this->admin)
            ->get(route('admin.whatsapp-log.index'))
            ->assertOk()
            ->assertSee('Riwayat Pesan WhatsApp')
            ->assertSee('Reservasi Baru');
    }

    public function test_customer_dashboard_shows_summary_from_database(): void
    {
        $reservasi = $this->createReservation();
        $this->completeService($reservasi);

        $this->actingAs($this->customer)
            ->get(route('booking-saya'))
            ->assertOk()
            ->assertViewHas('ringkasan', function (array $ringkasan) {
                return $ringkasan['reservasi_aktif'] === 1
                    && $ringkasan['servis_selesai'] === 1
                    && $ringkasan['kendaraan'] === 1
                    && (float) $ringkasan['total_biaya_servis'] === 150000.0;
            })
            ->assertSee('Reservasi Aktif')
            ->assertSee('Servis Selesai')
            ->assertSee('Total Biaya Servis')
            ->assertSee('Rp 150.000')
            ->assertSee('Servis Terakhir')
            ->assertSee('Slot Terdekat')
            ->assertSee('Kendaraan Saya')
            ->assertSee('scrollbar-width: none', false)
            ->assertSee($this->kendaraan->plat_nomor)
            ->assertDontSee('$12,628')
            ->assertDontSee('Order Statistics')
            ->assertDontSee('Profile Report')
            ->assertDontSee('YEAR 2022');
    }

    public function test_admin_can_send_confirmation_whatsapp_and_log_is_created(): void
    {
        $reservasi = $this->createReservation(['status' => StatusReservasi::Dikonfirmasi]);

        $response = $this->actingAs($this->admin)->post(route('admin.reservasi.kirim-wa', $reservasi), [
            'jenis' => 'konfirmasi_reservasi',
        ]);

        $response->assertRedirect();

        $message = $this->whatsappMessageFromRedirect($response);

        $this->assertStringContainsString($reservasi->kode, $message);
        $this->assertStringContainsString($this->slot->tanggal->translatedFormat('d M Y'), $message);
        $this->assertStringContainsString('23:00', $message);
        $this->assertStringContainsString('23:59', $message);
        $this->assertStringContainsString('disetujui', $message);
        $this->assertStringNotContainsString('Rp', $message);

        $this->assertDatabaseHas('whatsapp_logs', [
            'reservasi_id' => $reservasi->id,
            'jenis' => 'konfirmasi_reservasi',
            'nomor_tujuan' => '6281234567890',
            'status' => 'menunggu',
        ]);
    }

    public function test_admin_can_send_completed_service_whatsapp_without_cost(): void
    {
        $reservasi = $this->createReservation(['status' => StatusReservasi::Selesai]);
        $this->completeService($reservasi);

        $response = $this->actingAs($this->admin)->post(route('admin.reservasi.kirim-wa', $reservasi), [
            'jenis' => 'servis_selesai',
        ]);

        $response->assertRedirect();

        $message = $this->whatsappMessageFromRedirect($response);

        $this->assertStringContainsString($this->kendaraan->plat_nomor, $message);
        $this->assertStringContainsString('sudah selesai diservis', $message);
        $this->assertStringContainsString('Silakan diambil di bengkel', $message);
        $this->assertStringNotContainsString('Rp', $message);
        $this->assertStringNotContainsString('150.000', $message);

        $this->assertDatabaseHas('whatsapp_logs', [
            'reservasi_id' => $reservasi->id,
            'jenis' => 'servis_selesai',
            'status' => 'menunggu',
        ]);
    }

    public function test_admin_can_send_service_reminder_whatsapp(): void
    {
        $reservasi = $this->createReservation(['status' => StatusReservasi::Selesai]);
        $this->completeService($reservasi);

        $response = $this->actingAs($this->admin)->post(route('admin.reservasi.kirim-wa', $reservasi), [
            'jenis' => 'pengingat_servis',
        ]);

        $response->assertRedirect();

        $message = $this->whatsappMessageFromRedirect($response);

        $this->assertStringContainsString('sudah waktunya servis berkala', $message);
        $this->assertStringContainsString($this->layanan->nama, $message);
        $this->assertStringContainsString($this->kendaraan->plat_nomor, $message);

        $this->assertDatabaseHas('whatsapp_logs', [
            'reservasi_id' => $reservasi->id,
            'jenis' => 'pengingat_servis',
            'status' => 'menunggu',
        ]);
    }

    public function test_admin_can_send_rejection_whatsapp_after_rejecting_reservation(): void
    {
        $reservasi = $this->createReservation();

        $this->actingAs($this->admin)->patch(route('admin.reservasi.batalkan', $reservasi), [
            'alasan' => 'Slot sudah penuh.',
        ])->assertRedirect();

        $response = $this->actingAs($this->admin)->post(route('admin.reservasi.kirim-wa', $reservasi), [
            'jenis' => 'penolakan_reservasi',
        ]);

        $response->assertRedirect();

        $message = $this->whatsappMessageFromRedirect($response);

        $this->assertStringContainsString('ditolak', $message);
        $this->assertStringContainsString('Slot sudah penuh.', $message);
        $this->assertStringContainsString('Silakan pilih jadwal lain', $message);

        $this->assertSame(2, WhatsappLog::query()
            ->where('reservasi_id', $reservasi->id)
            ->where('jenis', 'penolakan_reservasi')
            ->count());
    }

    public function test_whatsapp_send_is_rejected_when_phone_is_invalid(): void
    {
        $this->customer->update(['no_hp' => null]);
        $reservasi = $this->createReservation(['status' => StatusReservasi::Dikonfirmasi]);

        $response = $this->actingAs($this->admin)->post(route('admin.reservasi.kirim-wa', $reservasi), [
            'jenis' => 'konfirmasi_reservasi',
        ]);

        $response->assertSessionHasErrors('whatsapp');
        $this->assertDatabaseMissing('whatsapp_logs', [
            'reservasi_id' => $reservasi->id,
            'jenis' => 'konfirmasi_reservasi',
        ]);
    }

    public function test_whatsapp_card_warns_when_phone_is_invalid(): void
    {
        $this->customer->update(['no_hp' => null]);
        $reservasi = $this->createReservation(['status' => StatusReservasi::Dikonfirmasi]);

        $this->actingAs($this->admin)
            ->get(route('admin.reservasi.index'))
            ->assertOk()
            ->assertSee('Nomor HP pelanggan belum diisi atau tidak valid.')
            ->assertDontSee('form-kirim-wa-'.$reservasi->id.'-konfirmasi_reservasi', false);
    }

    public function test_completed_whatsapp_is_rejected_when_service_is_not_finished(): void
    {
        $reservasi = $this->createReservation(['status' => StatusReservasi::Selesai]);

        $response = $this->actingAs($this->admin)->post(route('admin.reservasi.kirim-wa', $reservasi), [
            'jenis' => 'servis_selesai',
        ]);

        $response->assertSessionHasErrors('whatsapp');
        $this->assertDatabaseMissing('whatsapp_logs', [
            'reservasi_id' => $reservasi->id,
            'jenis' => 'servis_selesai',
        ]);
    }

    public function test_whatsapp_buttons_only_render_for_eligible_reservations(): void
    {
        $pending = $this->createReservation();

        $this->actingAs($this->admin)
            ->get(route('admin.reservasi.index'))
            ->assertOk()
            ->assertDontSee('form-kirim-wa-'.$pending->id.'-konfirmasi_reservasi', false);

        $confirmed = $this->createReservation(['status' => StatusReservasi::Dikonfirmasi]);

        $this->actingAs($this->admin)
            ->get(route('admin.reservasi.index'))
            ->assertOk()
            ->assertSee('form-kirim-wa-'.$confirmed->id.'-konfirmasi_reservasi', false);

        $completed = $this->createReservation(['status' => StatusReservasi::Selesai]);

        $this->actingAs($this->admin)
            ->get(route('admin.reservasi.index'))
            ->assertOk()
            ->assertDontSee('form-kirim-wa-'.$completed->id.'-servis_selesai', false);

        $this->completeService($completed);

        $this->actingAs($this->admin)
            ->get(route('admin.reservasi.index'))
            ->assertOk()
            ->assertSee('form-kirim-wa-'.$completed->id.'-servis_selesai', false)
            ->assertSee('form-kirim-wa-'.$completed->id.'-pengingat_servis', false);
    }

    public function test_rejection_whatsapp_button_renders_after_reservation_is_rejected(): void
    {
        $reservasi = $this->createReservation();

        $this->actingAs($this->admin)->patch(route('admin.reservasi.batalkan', $reservasi), [
            'alasan' => 'Jadwal penuh.',
        ])->assertRedirect();

        $this->actingAs($this->admin)
            ->get(route('admin.reservasi.index'))
            ->assertOk()
            ->assertSee('form-kirim-wa-'.$reservasi->id.'-penolakan_reservasi', false);
    }

    public function test_admin_can_filter_whatsapp_logs_by_date(): void
    {
        $reservasiLama = $this->createReservation([
            'kode' => 'RSV-LAMA-'.Str::upper(Str::random(4)),
            'status' => StatusReservasi::Dikonfirmasi,
        ]);
        $reservasiBaru = $this->createReservation([
            'kode' => 'RSV-BARU-'.Str::upper(Str::random(4)),
            'status' => StatusReservasi::Dikonfirmasi,
        ]);

        $logLama = app(WhatsappLogService::class)->reservationCreated($reservasiLama);
        $logBaru = app(WhatsappLogService::class)->reservationCreated($reservasiBaru);

        $logLama->forceFill(['created_at' => now()->subDays(10)])->save();
        $logBaru->forceFill(['created_at' => now()])->save();

        $this->actingAs($this->admin)
            ->get(route('admin.whatsapp-log.index', [
                'dari' => now()->subDays(2)->toDateString(),
                'sampai' => now()->toDateString(),
            ]))
            ->assertOk()
            ->assertSee($reservasiBaru->kode)
            ->assertDontSee($reservasiLama->kode)
            ->assertSee('name="dari"', false)
            ->assertSee('name="sampai"', false);
    }

    public function test_whatsapp_log_date_filter_requires_valid_range(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.whatsapp-log.index', [
                'dari' => '2026-10-10',
                'sampai' => '2026-10-01',
            ]))
            ->assertSessionHasErrors('sampai');
    }

    public function test_customer_can_book_with_new_vehicle_inline(): void
    {
        Event::fake([ReservationCreated::class]);

        $response = $this->actingAs($this->customer)->post(route('reservasi.store'), [
            'kendaraan_baru' => [
                'merk_motor_id' => $this->merk->id,
                'plat_nomor' => '  ab  7777  xy ',
                'model' => 'Vario 160',
                'tahun' => 2023,
            ],
            'id_slot' => $this->slot->id,
            'layanan' => [$this->layanan->id],
        ]);

        $response->assertRedirect(route('booking-saya'));

        $kendaraan = Kendaraan::query()->where('plat_nomor', 'AB 7777 XY')->firstOrFail();
        $reservasi = Reservasi::query()->where('user_id', $this->customer->id)->latest()->firstOrFail();

        $this->assertSame($this->customer->id, $kendaraan->user_id);
        $this->assertSame('Vario 160', $kendaraan->model);
        $this->assertSame(0, $kendaraan->kilometer);
        $this->assertSame($kendaraan->id, $reservasi->kendaraan_id);
    }

    public function test_customer_cannot_book_with_duplicate_new_vehicle_plate(): void
    {
        $response = $this->actingAs($this->customer)->post(route('reservasi.store'), [
            'kendaraan_baru' => [
                'merk_motor_id' => $this->merk->id,
                'plat_nomor' => Str::lower($this->kendaraan->plat_nomor),
                'model' => 'Motor Duplikat',
                'tahun' => 2023,
            ],
            'id_slot' => $this->slot->id,
            'layanan' => [$this->layanan->id],
        ]);

        $response->assertSessionHasErrors('kendaraan_baru.plat_nomor');
        $this->assertSame(1, Kendaraan::query()->where('plat_nomor', $this->kendaraan->plat_nomor)->count());
        $this->assertSame(0, Reservasi::query()->where('user_id', $this->customer->id)->count());
    }

    public function test_customer_can_cancel_confirmed_reservation(): void
    {
        $reservasi = $this->createReservation(['status' => StatusReservasi::Dikonfirmasi]);

        $this->actingAs($this->customer)
            ->post(route('reservasi.cancel', $reservasi))
            ->assertRedirect();

        $this->assertSame(StatusReservasi::Dibatalkan, $reservasi->fresh()->status);
        $this->assertDatabaseHas('whatsapp_logs', [
            'reservasi_id' => $reservasi->id,
            'jenis' => 'pembatalan_reservasi',
            'status' => 'menunggu',
        ]);
    }

    public function test_customer_cannot_cancel_completed_reservation(): void
    {
        $reservasi = $this->createReservation(['status' => StatusReservasi::Selesai]);

        $this->actingAs($this->customer)
            ->post(route('reservasi.cancel', $reservasi))
            ->assertForbidden();

        $this->assertSame(StatusReservasi::Selesai, $reservasi->fresh()->status);
    }

    public function test_booking_page_shows_rejection_reason(): void
    {
        $reservasi = $this->createReservation([
            'status' => StatusReservasi::Dibatalkan,
            'catatan' => 'Ditolak admin: Jadwal bengkel penuh.',
        ]);

        $this->actingAs($this->customer)
            ->get(route('booking-saya'))
            ->assertOk()
            ->assertSee($reservasi->kode)
            ->assertSee('Ditolak')
            ->assertSee('Jadwal bengkel penuh.');
    }

    public function test_reservation_detail_shows_services_and_rejection_reason(): void
    {
        $reservasi = $this->createReservation([
            'status' => StatusReservasi::Dibatalkan,
            'catatan' => 'Ditolak admin: Sparepart tidak tersedia.',
        ]);

        $this->actingAs($this->customer)
            ->get(route('reservasi.show', $reservasi))
            ->assertOk()
            ->assertSee($reservasi->kode)
            ->assertSee($this->layanan->nama)
            ->assertSee('Ditolak')
            ->assertSee('Sparepart tidak tersedia.')
            ->assertSee('Biaya menyesuaikan merk dan kondisi motor, dikonfirmasi bengkel.');
    }

    public function test_customer_cannot_view_another_customers_reservation_detail(): void
    {
        $other = User::create([
            'name' => 'Pelanggan Lain',
            'no_hp' => '081299999999',
            'email' => 'lain-'.Str::lower(Str::random(8)).'@example.test',
            'password' => 'password',
            'role' => UserRole::Pelanggan,
            'aktif' => true,
        ]);

        $reservasi = $this->createReservation();

        $this->actingAs($other)
            ->get(route('reservasi.show', $reservasi))
            ->assertForbidden();
    }

    public function test_booking_page_preselects_slot_from_public_page(): void
    {
        $this->actingAs($this->customer)
            ->get(route('booking-saya', [
                'slot' => $this->slot->id,
                'tanggal' => $this->slot->tanggal->toDateString(),
            ]))
            ->assertOk()
            ->assertViewHas('requestedSlotId', $this->slot->id)
            ->assertSee('value="'.$this->slot->id.'"', false)
            ->assertSee('modalBuatReservasi', false);
    }

    public function test_booking_page_shows_check_in_status(): void
    {
        $this->createReservation(['status' => StatusReservasi::CheckIn]);

        $this->actingAs($this->customer)
            ->get(route('booking-saya'))
            ->assertOk()
            ->assertSee('Check In');
    }

    public function test_booking_page_offers_new_vehicle_form_when_customer_has_no_vehicles(): void
    {
        $this->kendaraan->delete();

        $this->actingAs($this->customer)
            ->get(route('booking-saya'))
            ->assertOk()
            ->assertSee('Tambah kendaraan baru')
            ->assertSee('Data Kendaraan Baru')
            ->assertSee('name="kendaraan_baru[plat_nomor]"', false);
    }

    public function test_admin_pages_show_check_in_status_label(): void
    {
        $this->createReservation(['status' => StatusReservasi::CheckIn]);

        $this->actingAs($this->admin)
            ->get(route('admin'))
            ->assertOk()
            ->assertSee('Check In');

        $this->actingAs($this->admin)
            ->get(route('admin.reservasi.index'))
            ->assertOk()
            ->assertSee('Check In');
    }

    private function completeService(Reservasi $reservasi, array $attributes = []): Servis
    {
        return Servis::create(array_merge([
            'reservasi_id' => $reservasi->id,
            'kendaraan_id' => $this->kendaraan->id,
            'tanggal_masuk' => $this->slot->tanggal->toDateString(),
            'tanggal_selesai' => $this->slot->tanggal->toDateString(),
            'status' => StatusServis::Selesai,
            'tindakan' => 'Servis rutin',
            'total_biaya' => 150000,
        ], $attributes));
    }

    private function whatsappMessageFromRedirect(TestResponse $response): string
    {
        $location = $response->getTargetUrl();

        $this->assertStringStartsWith('https://wa.me/', $location);

        parse_str((string) parse_url($location, PHP_URL_QUERY), $query);

        return (string) ($query['text'] ?? '');
    }

    private function createReservation(array $attributes = []): Reservasi
    {
        $reservasi = Reservasi::create(array_merge([
            'user_id' => $this->customer->id,
            'kendaraan_id' => $this->kendaraan->id,
            'id_slot' => $this->slot->id,
            'kode' => 'RSV-TEST-'.Str::upper(Str::random(6)),
            'status' => StatusReservasi::MenungguKonfirmasi,
            'keluhan' => 'Tes reservasi',
        ], $attributes));

        $reservasi->layanan()->attach($this->layanan->id, ['jumlah' => 1]);

        return $reservasi;
    }
}
