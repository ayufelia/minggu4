<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirController extends Controller
{
    public function index()
    {
        return view('kasir');
    }

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
                'message' => 'Uang pembayaran belum mencukupi.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $transactionNumber =
                'TRX-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));

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
                        'Stok produk "' .
                        $product->name .
                        '" tidak mencukupi.'
                    );
                }

                $subtotalItem =
                    $product->price * $item['qty'];

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

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil disimpan.',
                'transaction_id' => $transactionId,
                'redirect' => url('/kasir/struk/' . $transactionId),
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

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

        // File berada di:
        // resources/views/struk.blade.php
        return view('struk', compact('transaction', 'details'));
    }
}