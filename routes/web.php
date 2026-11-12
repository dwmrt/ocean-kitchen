<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KatalogController;

Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');
Route::get('/', [HomeController::class, 'index'])->name('home');