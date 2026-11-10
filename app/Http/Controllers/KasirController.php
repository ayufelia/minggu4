<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Events\OrderPlaced;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

class KasirController extends Controller
{
    // Menampilkan halaman kasir
    public function index()
    {
        return view('kasir');
    }

    // Mencari produk berdasarkan nama atau kode produk
    public function searchProduct(Request $request)
    {
        $keyword = trim($request->input('keyword', ''));

        $products = DB::table('products')
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('sku', 'like', '%' . $keyword . '%');
            })
            ->select(
                'id',
                'name',
                'sku',
                'price',
                'stock'
            )
            ->orderBy('name', 'asc')
            ->get();

        return response()->json($products);
    }

    // Menyimpan transaksi kasir
    public function storeTransaction(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.qty' => 'required|integer|min:1',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'other_fee' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'change_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:50',
        ]);

        if ($request->paid_amount < $request->grand_total) {
            return response()->json([
                'success' => false,
                'message' => 'Uang pembayaran belum mencukupi.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Membuat nomor transaksi
            $transactionNumber = 'TRX-' . date('YmdHis') . '-'
                . strtoupper(substr(uniqid(), -4));

            // Menyimpan transaksi
            $transactionId = DB::table('transactions')->insertGetId([
                'transaction_number' => $transactionNumber,
                'user_id' => Auth::id(),
                'customer_id' => null,
                'subtotal' => $request->subtotal,
                'discount' => $request->discount,
                'tax' => $request->tax,
                'other_fee' => $request->other_fee,
                'grand_total' => $request->grand_total,
                'paid_amount' => $request->paid_amount,
                'change_amount' => $request->change_amount,
                'payment_method' => $request->payment_method,
                'status' => 'paid',
                'created_at' => now(),
            ]);

            // Menyimpan detail barang dan mengurangi stok
            foreach ($request->items as $item) {

                $product = DB::table('products')
                    ->where('id', $item['id'])
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw new \Exception('Produk tidak ditemukan.');
                }

                if ($product->stock < $item['qty']) {
                    throw new \Exception(
                        'Stok produk "' . $product->name
                        . '" tidak mencukupi.'
                    );
                }

                $subtotalItem = $product->price * $item['qty'];

                DB::table('transaction_details')->insert([
                    'transaction_id' => $transactionId,
                    'product_id' => $product->id,
                    'product_code' => $product->sku,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'qty' => $item['qty'],
                    'discount' => 0,
                    'subtotal' => $subtotalItem,
                    'created_at' => now(),
                ]);

                DB::table('products')
                    ->where('id', $product->id)
                    ->decrement('stock', $item['qty']);
            }

            // Menyelesaikan transaksi database
            DB::commit();

            // Mengambil data transaksi yang sudah tersimpan
            $transaction = DB::table('transactions')
                ->where('id', $transactionId)
                ->first();

            // Mengirim event broadcast ke Laravel Reverb
            OrderPlaced::dispatch($transaction);

            // Mencetak struk secara otomatis ke file simulasi
            $printStatus = 'success';
            $printMessage = 'Struk berhasil dibuat.';

            try {
                $this->printReceiptToFile($transactionId);
            } catch (\Throwable $printError) {
                $printStatus = 'failed';
                $printMessage = 'Transaksi berhasil, tetapi struk gagal dibuat: '
                    . $printError->getMessage();
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan.',
                'transaction_id' => $transactionId,
                'print_status' => $printStatus,
                'print_message' => $printMessage,
                'redirect' => url('/kasir/struk/' . $transactionId),
            ]);

        } catch (\Throwable $e) {

            // Rollback jika transaksi database masih berjalan
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Membuat struk menggunakan printer ESC/POS simulasi
    private function printReceiptToFile($id)
    {
        // Mengambil data transaksi
        $transaction = DB::table('transactions')
            ->where('id', $id)
            ->first();

        if (!$transaction) {
            throw new \Exception('Transaksi tidak ditemukan.');
        }

        // Mengambil detail barang
        $details = DB::table('transaction_details')
            ->where('transaction_id', $id)
            ->get();

        // Membuat folder penyimpanan struk
        $directory = storage_path('app/receipts');

        File::ensureDirectoryExists($directory);

        // Menentukan lokasi file struk
        $filePath = $directory . '/receipt-' . $id . '.prn';

        // Membuat koneksi printer simulasi
        $connector = new FilePrintConnector($filePath);
        $printer = new Printer($connector);

        try {
            // Judul struk
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->text("TOKO RETAIL MAKMUR\n");
            $printer->setEmphasis(false);
            $printer->text("Jl. Contoh No. 123 - Jember\n");

            $printer->text("--------------------------------\n");

            // Informasi transaksi
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text(
                "No: " . $transaction->transaction_number . "\n"
            );

            $printer->text(
                "Tanggal: " . $transaction->created_at . "\n"
            );

            $printer->text("--------------------------------\n");

            // Daftar barang
            foreach ($details as $item) {
                $printer->text($item->product_name . "\n");

                $printer->text(
                    $item->qty . " x Rp"
                    . number_format($item->price, 0, ',', '.')
                    . " = Rp"
                    . number_format($item->subtotal, 0, ',', '.')
                    . "\n"
                );
            }

            $printer->text("--------------------------------\n");

            // Rincian pembayaran
            $printer->text(
                "Subtotal: Rp"
                . number_format($transaction->subtotal, 0, ',', '.')
                . "\n"
            );

            $printer->text(
                "Diskon: Rp"
                . number_format($transaction->discount, 0, ',', '.')
                . "\n"
            );

            $printer->text(
                "Pajak: Rp"
                . number_format($transaction->tax, 0, ',', '.')
                . "\n"
            );

            $printer->text(
                "Biaya lain: Rp"
                . number_format($transaction->other_fee, 0, ',', '.')
                . "\n"
            );

            // Total pembayaran
            $printer->setEmphasis(true);

            $printer->text(
                "TOTAL: Rp"
                . number_format($transaction->grand_total, 0, ',', '.')
                . "\n"
            );

            $printer->setEmphasis(false);

            $printer->text(
                "Bayar: Rp"
                . number_format($transaction->paid_amount, 0, ',', '.')
                . "\n"
            );

            $printer->text(
                "Kembali: Rp"
                . number_format($transaction->change_amount, 0, ',', '.')
                . "\n"
            );

            $printer->text(
                "Metode: " . $transaction->payment_method . "\n"
            );

            // Penutup struk
            $printer->text("--------------------------------\n");

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima kasih telah berbelanja!\n");

            // Memberi jarak dan memotong kertas
            $printer->feed(3);
            $printer->cut();

        } finally {
            // Menutup koneksi printer
            $printer->close();
        }

        return $filePath;
    }

    // Menampilkan struk transaksi
    public function receipt($id)
    {
        $transaction = DB::table('transactions')
            ->where('id', $id)
            ->first();

        if (!$transaction) {
            abort(404, 'Transaksi tidak ditemukan.');
        }

        $details = DB::table('transaction_details')
            ->where('transaction_id', $id)
            ->get();

        return view('struk', compact(
            'transaction',
            'details'
        ));
    }

    // Membuat dan mengunduh invoice PDF
    public function invoicePdf($id)
    {
        // Mengambil transaksi
        $transaction = DB::table('transactions')
            ->where('id', $id)
            ->first();

        if (!$transaction) {
            abort(404, 'Transaksi tidak ditemukan.');
        }

        // Mengambil detail barang transaksi
        $details = DB::table('transaction_details')
            ->where('transaction_id', $id)
            ->get();

        // Menyesuaikan format barang dengan invoice.blade.php
        $items = $details->map(function ($item) {
            return [
                'name' => $item->product_name ?? 'Produk',
                'quantity' => $item->qty ?? 0,
                'price' => $item->price ?? 0,
                'subtotal' => $item->subtotal ?? 0,
            ];
        })->toArray();

        // Nomor invoice
        $invoiceNumber = 'INV-' . $transaction->transaction_number;

        // Tanggal transaksi
        $date = isset($transaction->created_at)
            ? date('d/m/Y H:i', strtotime($transaction->created_at))
            : date('d/m/Y H:i');

        // Nama pelanggan
        $customer = $transaction->customer_name
            ?? 'Pelanggan Umum';

        // Total pembayaran
        $total = $transaction->grand_total ?? 0;

        // Mengirim semua variabel ke tampilan invoice
        $pdf = Pdf::loadView('invoices.invoice', [
            'transaction' => $transaction,
            'details' => $details,
            'items' => $items,
            'invoiceNumber' => $invoiceNumber,
            'date' => $date,
            'customer' => $customer,
            'total' => $total,
        ]);

        // Mengunduh invoice PDF
        return $pdf->download(
            'invoice-' . $transaction->transaction_number . '.pdf'
        );
    }
}