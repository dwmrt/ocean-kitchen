<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PeriodePoController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TarifOngkirController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PesananController;

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
Route::post('/pesan', [OrderController::class, 'createOrder'])
    ->middleware('throttle:10,1')
    ->name('pesan.simpan');
Route::get('/pesanan/{kode}/bayar', [PesananController::class, 'bayar'])
    ->middleware('throttle:60,1')
    ->name('pesanan.bayar');
Route::post('/pesanan/{kode}/bukti', [OrderController::class, 'uploadBukti'])
    ->middleware('throttle:10,1')
    ->name('pesanan.bukti');

require __DIR__.'/admin.php';
