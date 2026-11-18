<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PeriodePoController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TarifOngkirController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [ProdukController::class, 'getKatalog'])->name('katalog');
Route::get('/produk/{id}', [ProdukController::class, 'getDetailProduk'])
    ->whereNumber('id')
    ->name('produk.detail');
Route::get('/api/periode-aktif', [PeriodePoController::class, 'getPeriodeAktif'])->name('periode.aktif');
Route::get('/api/tarif-ongkir', [TarifOngkirController::class, 'getTarifOngkir'])->name('tarif.ongkir');

require __DIR__.'/admin.php';