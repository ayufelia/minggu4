<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function generate()
    {
        // Data contoh invoice
        $invoiceNumber = 'INV-' . date('YmdHis');
        $date = date('d-m-Y');
        $customer = 'Pelanggan Umum';

        // Daftar produk contoh
        $items = [
            [
                'name' => 'Beras 5 Kg',
                'quantity' => 2,
                'price' => 75000,
            ],
            [
                'name' => 'Minyak Goreng',
                'quantity' => 1,
                'price' => 18000,
            ],
            [
                'name' => 'Gula Pasir',
                'quantity' => 2,
                'price' => 16000,
            ],
        ];

        // Menghitung total pembayaran
        $total = 0;

        foreach ($items as $item) {
            $total += $item['quantity'] * $item['price'];
        }

        // Membuat PDF dari tampilan invoice
        $pdf = Pdf::loadView('invoices.invoice', compact(
            'invoiceNumber',
            'date',
            'customer',
            'items',
            'total'
        ));

        // Mengunduh file PDF
        return $pdf->download('invoice.pdf');
    }
}
