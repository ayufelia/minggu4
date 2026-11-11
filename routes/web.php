<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MidtransController;


// ========================================
// LOGIN
// ========================================

Route::get('/login', [AuthController::class, 'login'])
    ->name('login');

Route::post('/login', [AuthController::class, 'prosesLogin'])
    ->name('login.proses');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


// ========================================
// NOTIFIKASI MIDTRANS
// Di luar middleware auth
// ========================================

Route::post('/midtrans/notification', [
    MidtransController::class,
    'notification'
])->name('midtrans.notification');


// ========================================
// ROUTE YANG MEMERLUKAN LOGIN
// ========================================

Route::middleware('auth')->group(function () {

    // DASHBOARD
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');


    // PRODUK
    Route::resource('products', ProductController::class);


    // ====================================
    // KASIR
    // ====================================

    Route::get('/kasir', [KasirController::class, 'index'])
        ->name('kasir.index');

    Route::get('/kasir/search-product', [KasirController::class, 'searchProduct'])
        ->name('kasir.search-product');

    Route::post('/kasir/transaksi', [KasirController::class, 'storeTransaction'])
        ->name('kasir.transaksi');

    Route::get('/kasir/struk/{id}', [KasirController::class, 'receipt'])
        ->name('kasir.struk');

    Route::get('/kasir/invoice/{id}', [KasirController::class, 'invoicePdf'])
        ->name('kasir.invoice');

    Route::get('/invoice/pdf', [InvoiceController::class, 'generate'])
        ->name('invoice.pdf');


    // ====================================
    // PEMBAYARAN MIDTRANS
    // ====================================

    Route::post('/kasir/midtrans/create-payment', [
        MidtransController::class,
        'createPayment'
    ])->name('kasir.midtrans.create-payment');

});