<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\MerkMotorController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\ReservasiController;
use App\Http\Controllers\Admin\ServisController;
use App\Http\Controllers\Admin\SlotWaktuController;
use App\Http\Controllers\Admin\WhatsappLogController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\KendaraanController;
use App\Http\Controllers\Customer\ReservationController;
use App\Http\Controllers\Customer\RiwayatServisController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicSlotController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/cek-slot', [PublicSlotController::class, 'index'])->name('slot.publik');

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/admin', [DashboardController::class, 'index'])->middleware('role:admin')->name('admin');
    Route::get('/booking-saya', [CustomerDashboardController::class, 'index'])->middleware('role:pelanggan')->name('booking-saya');
    Route::get('/riwayat-servis', [RiwayatServisController::class, 'index'])->middleware('role:pelanggan')->name('riwayat-servis.index');

    Route::post('/reservasi', [ReservationController::class, 'store'])->middleware('role:pelanggan')->name('reservasi.store');
    Route::get('/reservasi/{reservasi}', [CustomerDashboardController::class, 'show'])->middleware('role:pelanggan')->name('reservasi.show');
    Route::post('/reservasi/{reservasi}/batalkan', [ReservationController::class, 'cancel'])->middleware('role:pelanggan')->name('reservasi.cancel');

    Route::resource('kendaraan', KendaraanController::class)
        ->except('show')
        ->middleware('role:pelanggan');

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::resource('merk-motor', MerkMotorController::class)->except('show')->parameters(['merk-motor' => 'merk_motor']);
        Route::resource('layanan', LayananController::class)->except('show');
        Route::resource('slot-waktu', SlotWaktuController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::get('reservasi', [ReservasiController::class, 'index'])->name('reservasi.index');
        Route::get('reservasi/notifikasi', [ReservasiController::class, 'notifications'])->name('reservasi.notifications');
        Route::patch('reservasi/{reservasi}/konfirmasi', [ReservasiController::class, 'konfirmasi'])->name('reservasi.konfirmasi');
        Route::patch('reservasi/{reservasi}/batalkan', [ReservasiController::class, 'batalkan'])->name('reservasi.batalkan');
        Route::patch('reservasi/{reservasi}/check-in', [ServisController::class, 'checkIn'])->name('reservasi.check-in');
        Route::post('reservasi/{reservasi}/kirim-wa', [ReservasiController::class, 'kirimWhatsapp'])->name('reservasi.kirim-wa');
        Route::get('servis', [ServisController::class, 'index'])->name('servis.index');
        Route::patch('servis/{servis}', [ServisController::class, 'update'])->name('servis.update');
        Route::patch('servis/{servis}/status', [ServisController::class, 'status'])->name('servis.status');
        Route::get('whatsapp-log', [WhatsappLogController::class, 'index'])->name('whatsapp-log.index');
        Route::get('pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
        Route::get('pelanggan/create', [PelangganController::class, 'create'])->name('pelanggan.create');
        Route::post('pelanggan', [PelangganController::class, 'store'])->name('pelanggan.store');
        Route::get('pelanggan/{pelanggan}/edit', [PelangganController::class, 'edit'])->name('pelanggan.edit');
        Route::put('pelanggan/{pelanggan}', [PelangganController::class, 'update'])->name('pelanggan.update');
        Route::patch('pelanggan/{pelanggan}/toggle', [PelangganController::class, 'toggle'])->name('pelanggan.toggle');
        Route::patch('pelanggan/{pelanggan}/reset-password', [PelangganController::class, 'resetPassword'])->name('pelanggan.reset-password');
    });
});
