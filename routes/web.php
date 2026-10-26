<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use App\Enums\StatusReservasi;
use App\Models\Reservasi;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::get('/admin', fn () => view('home'))->middleware('role:admin')->name('admin');
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
});
