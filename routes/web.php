<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [ProdukController::class, 'getKatalog'])->name('katalog');
Route::get('/produk/{id}', [ProdukController::class, 'getDetailProduk'])
    ->whereNumber('id')
    ->name('produk.detail');

require __DIR__.'/admin.php';