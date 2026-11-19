<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PeriodePoController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TarifOngkirController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [ProdukController::class, 'getKatalog'])->name('katalog');
Route::get('/produk/{id}', [ProdukController::class, 'getDetailProduk'])
    ->whereNumber('id')
    ->name('produk.detail');
Route::get('/api/periode-aktif', [PeriodePoController::class, 'getPeriodeAktif'])->name('periode.aktif');
Route::get('/api/tarif-ongkir', [TarifOngkirController::class, 'getTarifOngkir'])->name('tarif.ongkir');
Route::post('/api/hitung-total', [OrderController::class, 'hitungTotal'])
    ->middleware('throttle:60,1')
    ->name('order.hitung-total');

require __DIR__.'/admin.php';
