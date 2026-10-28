<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\InvoiceController;


// ==============================
// LOGIN
// ==============================

Route::get('/login', [AuthController::class, 'login'])
    ->name('login');

Route::post('/login', [AuthController::class, 'prosesLogin'])
    ->name('login.proses');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


// ==============================
// ROUTE YANG MEMERLUKAN LOGIN
// ==============================

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index']);


    // ==============================
    // PRODUK
    // ==============================

    Route::resource('products', ProductController::class);


    // ==============================
    // KASIR
    // ==============================

    // Halaman kasir
    Route::get('/kasir', [KasirController::class, 'index'])
        ->name('kasir.index');


    // Cari produk
    Route::get('/kasir/search-product', [
        KasirController::class,
        'searchProduct'
    ])->name('kasir.search-product');


    // Simpan transaksi
    Route::post('/kasir/transaksi', [
        KasirController::class,
        'storeTransaction'
    ])->name('kasir.transaksi');


    // Tampilkan struk
    Route::get('/kasir/struk/{id}', [
        KasirController::class,
        'receipt'
    ])->name('kasir.struk');


    // Cetak invoice PDF berdasarkan transaksi
    Route::get('/kasir/invoice/{id}', [
        KasirController::class,
        'invoicePdf'
    ])->name('kasir.invoice');


    // ==============================
    // ACARA 30 - INVOICE PDF
    // ==============================

    // Invoice contoh dari InvoiceController
    Route::get('/invoice/pdf', [
        InvoiceController::class,
        'generate'
    ])->name('invoice.pdf');

});