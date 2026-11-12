<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

/*
| Rute admin. Semua rute admin lain (produk, periode PO, pesanan, dst)
| ditambahkan di dalam grup "auth:admin" di bawah ini.
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Hanya untuk yang BELUM login.
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');

        // Maksimal 5 percobaan per menit (mencegah tebak password).
        Route::post('login', [AuthController::class, 'login'])
            ->middleware('throttle:5,1')
            ->name('login.submit');
    });

    // Hanya untuk admin yang SUDAH login.
    Route::middleware('auth:admin')->group(function () {
        Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        // TODO: rute produk, periode PO, pesanan, bahan baku, laporan.
    });
});
