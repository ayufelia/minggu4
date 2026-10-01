<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KasirController;


Route::get('/login', [AuthController::class, 'login'])
    ->name('login');


Route::post('/login', [AuthController::class, 'prosesLogin'])
    ->name('login.proses');


Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index']);


    Route::resource('products', ProductController::class);


    // ==============================
    // KASIR
    // ==============================

    Route::get('/kasir', [KasirController::class, 'index']);


    Route::get('/kasir/search-product', [KasirController::class, 'searchProduct'])
        ->name('kasir.search-product');


    // Simpan transaksi
    Route::post('/kasir/transaksi', [KasirController::class, 'storeTransaction'])
        ->name('kasir.transaksi');


    // Tampilkan struk
    Route::get('/kasir/struk/{id}', [KasirController::class, 'receipt'])
        ->name('kasir.struk');

});