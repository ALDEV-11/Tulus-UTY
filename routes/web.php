<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use App\Enums\StatusReservasi;
use App\Models\Reservasi;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\MerkMotorController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\SlotWaktuController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/admin', fn () => view('admin.dashboard'))->middleware('role:admin')->name('admin');
    Route::get('/booking-saya', fn () => view('home'))->middleware('role:pelanggan')->name('booking-saya');

    Route::get('/reservasi/{reservasi}', function (Reservasi $reservasi) {
        Gate::authorize('view', $reservasi);

        return view('home');
    })->middleware('role:pelanggan')->name('reservasi.show');

    Route::post('/reservasi/{reservasi}/batalkan', function (Reservasi $reservasi) {
        Gate::authorize('cancel', $reservasi);
        $reservasi->update(['status' => StatusReservasi::Dibatalkan]);

        return back()->with('status', 'Reservasi berhasil dibatalkan.');
    })->middleware('role:pelanggan')->name('reservasi.cancel');

    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::resource('merk-motor', MerkMotorController::class)->except('show')->parameters(['merk-motor' => 'merk_motor']);
        Route::resource('layanan', LayananController::class)->except('show');
        Route::resource('slot-waktu', SlotWaktuController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::get('pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
        Route::patch('pelanggan/{pelanggan}/toggle', [PelangganController::class, 'toggle'])->name('pelanggan.toggle');
        Route::patch('pelanggan/{pelanggan}/reset-password', [PelangganController::class, 'resetPassword'])->name('pelanggan.reset-password');
    });
});
