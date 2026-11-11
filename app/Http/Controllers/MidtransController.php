<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransController extends Controller
{
    // =========================================
    // KONFIGURASI MIDTRANS
    // =========================================

    private function configureMidtrans()
    {
        Config::$serverKey = config('midtrans.serverKey');
        Config::$isProduction = config('midtrans.isProduction');
        Config::$isSanitized = config('midtrans.isSanitized');
        Config::$is3ds = config('midtrans.is3ds');
    }

    // =========================================
    // MEMBUAT PEMBAYARAN MIDTRANS
    // =========================================

    public function createPayment(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.qty' => 'required|integer|min:1',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'other_fee' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
        ]);

        try {
            $this->configureMidtrans();

            if (empty(Config::$serverKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Server Key Midtrans belum diatur.',
                ], 500);
            }

            $transactionId = null;

            DB::transaction(function () use ($request, &$transactionId) {

                $subtotal = 0;
                $details = [];

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
                            'Stok produk ' . $product->name .
                            ' tidak mencukupi.'
                        );
                    }

                    $price = (float) $product->price;
                    $qty = (int) $item['qty'];
                    $itemSubtotal = $price * $qty;

                    $subtotal += $itemSubtotal;

                    $details[] = [
                        'product_id' => $product->id,
                        'product_code' => $product->sku ?? '',
                        'product_name' => $product->name,
                        'price' => $price,
                        'qty' => $qty,
                        'discount' => 0,
                        'subtotal' => $itemSubtotal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $discount = (float) ($request->discount ?? 0);
                $tax = (float) ($request->tax ?? 0);
                $otherFee = (float) ($request->other_fee ?? 0);

                if ($discount > $subtotal) {
                    throw new \Exception(
                        'Diskon tidak boleh melebihi subtotal.'
                    );
                }

                $grandTotal = $subtotal - $discount + $tax + $otherFee;

                if ($grandTotal <= 0) {
                    throw new \Exception(
                        'Total pembayaran tidak valid.'
                    );
                }

                $transactionNumber = 'TRX-' .
                    date('YmdHis') . '-' .
                    strtoupper(Str::random(5));

                $transactionId = DB::table('transactions')->insertGetId([
                    'transaction_number' => $transactionNumber,
                    'user_id' => auth()->id(),
                    'customer_id' => null,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'tax' => $tax,
                    'other_fee' => $otherFee,
                    'grand_total' => $grandTotal,
                    'paid_amount' => 0,
                    'change_amount' => 0,
                    'payment_method' => 'Midtrans',
                    'status' => 'pending',
                    'midtrans_order_id' => $transactionNumber,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($details as $detail) {
                    $detail['transaction_id'] = $transactionId;

                    DB::table('transaction_details')
                        ->insert($detail);
                }
            });

            $transaction = DB::table('transactions')
                ->where('id', $transactionId)
                ->first();

            $snapPayload = [
                'transaction_details' => [
                    'order_id' => $transaction->midtrans_order_id,
                    'gross_amount' => (int) round(
                        $transaction->grand_total
                    ),
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name ?? 'Pelanggan',
                    'email' => auth()->user()->email ?? '',
                ],
            ];

            $snapResponse = Snap::createTransaction($snapPayload);

            DB::table('transactions')
                ->where('id', $transactionId)
                ->update([
                    'snap_token' => $snapResponse->token,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pembayaran berhasil dibuat.',
                'transaction_id' => $transactionId,
                'snap_token' => $snapResponse->token,
                'redirect_url' => $snapResponse->redirect_url,
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // =========================================
    // NOTIFIKASI PEMBAYARAN MIDTRANS
    // =========================================

    public function notification(Request $request)
    {
        try {
            $this->configureMidtrans();

            $serverKey = config('midtrans.serverKey');

            if (empty($serverKey)) {
                throw new \Exception(
                    'Server Key Midtrans belum diatur.'
                );
            }

            // Mengambil data notifikasi
            $orderId = (string) $request->input('order_id');
            $statusCode = (string) $request->input('status_code');
            $grossAmount = (string) $request->input('gross_amount');
            $signatureKey = (string) $request->input('signature_key');

            $transactionStatus = (string) $request->input(
                'transaction_status'
            );

            $fraudStatus = $request->input('fraud_status');

            // Memastikan data notifikasi lengkap
            if (
                empty($orderId) ||
                empty($statusCode) ||
                empty($grossAmount) ||
                empty($signatureKey)
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data notifikasi tidak lengkap.',
                ], 400);
            }

            // =====================================
            // VERIFIKASI SIGNATURE MIDTRANS
            // =====================================

            $expectedSignature = hash(
                'sha512',
                $orderId . $statusCode . $grossAmount . $serverKey
            );

            if (!hash_equals($expectedSignature, $signatureKey)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Signature notifikasi tidak valid.',
                ], 403);
            }

            // =====================================
            // PROSES STATUS TRANSAKSI
            // =====================================

            DB::transaction(function () use (
                $orderId,
                $statusCode,
                $grossAmount,
                $transactionStatus,
                $fraudStatus
            ) {

                $transaction = DB::table('transactions')
                    ->where('midtrans_order_id', $orderId)
                    ->lockForUpdate()
                    ->first();

                if (!$transaction) {
                    throw new \Exception(
                        'Transaksi tidak ditemukan.'
                    );
                }

                // Pastikan nominal pembayaran sesuai
                if (
                    abs(
                        (float) $grossAmount -
                        (float) $transaction->grand_total
                    ) > 0.01
                ) {
                    throw new \Exception(
                        'Nominal pembayaran tidak sesuai.'
                    );
                }

                // Jangan mengubah transaksi yang sudah dibayar
                if ($transaction->stock_deducted_at !== null) {
                    return;
                }

                // =================================
                // PEMBAYARAN BERHASIL
                // =================================

                if (
                    in_array(
                        $transactionStatus,
                        ['capture', 'settlement']
                    )
                ) {

                    // Status pembayaran sukses harus memiliki
                    // status code 200.
                    if ($statusCode !== '200') {
                        return;
                    }

                    // Jika fraud_status tersedia, harus accept.
                    if (
                        $fraudStatus !== null &&
                        $fraudStatus !== '' &&
                        $fraudStatus !== 'accept'
                    ) {
                        return;
                    }

                    // Capture hanya diterima jika fraud accept.
                    if (
                        $transactionStatus === 'capture' &&
                        $fraudStatus !== 'accept'
                    ) {
                        return;
                    }

                    $details = DB::table('transaction_details')
                        ->where('transaction_id', $transaction->id)
                        ->get();

                    foreach ($details as $detail) {

                        $product = DB::table('products')
                            ->where('id', $detail->product_id)
                            ->lockForUpdate()
                            ->first();

                        if (!$product) {
                            throw new \Exception(
                                'Produk ' . $detail->product_name .
                                ' tidak ditemukan.'
                            );
                        }

                        if ($product->stock < $detail->qty) {
                            throw new \Exception(
                                'Stok produk ' . $product->name .
                                ' tidak mencukupi.'
                            );
                        }

                        DB::table('products')
                            ->where('id', $product->id)
                            ->decrement('stock', $detail->qty);
                    }

                    // Mengubah status transaksi menjadi paid
                    DB::table('transactions')
                        ->where('id', $transaction->id)
                        ->update([
                            'status' => 'paid',
                            'paid_amount' => $transaction->grand_total,
                            'change_amount' => 0,
                            'paid_at' => now(),
                            'stock_deducted_at' => now(),
                            'updated_at' => now(),
                        ]);
                }

                // =================================
                // PEMBAYARAN GAGAL / DIBATALKAN
                // =================================

                elseif (
                    in_array(
                        $transactionStatus,
                        ['deny', 'cancel', 'expire', 'failure']
                    )
                ) {

                    DB::table('transactions')
                        ->where('id', $transaction->id)
                        ->update([
                            'status' => $transactionStatus,
                            'updated_at' => now(),
                        ]);
                }

                // =================================
                // PEMBAYARAN MASIH MENUNGGU
                // =================================

                elseif ($transactionStatus === 'pending') {

                    DB::table('transactions')
                        ->where('id', $transaction->id)
                        ->update([
                            'status' => 'pending',
                            'updated_at' => now(),
                        ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi pembayaran berhasil diproses.',
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}