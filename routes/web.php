<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\KatalogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');

require __DIR__.'/admin.php';