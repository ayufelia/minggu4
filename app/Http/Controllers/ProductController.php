<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'products.*',
                'categories.name as category_name'
            )
            ->get();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = DB::table('categories')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
            'sku' => 'required|unique:products,sku',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        DB::table('products')->insert([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'sku' => $request->sku,
            'price' => $request->price,
            'stock' => $request->stock,
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $product = DB::table('products')
            ->where('id', $id)
            ->first();

        $categories = DB::table('categories')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
            'sku' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
        ]);

        DB::table('products')
            ->where('id', $id)
            ->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'sku' => $request->sku,
                'price' => $request->price,
                'stock' => $request->stock,
            ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::table('products')
            ->where('id', $id)
            ->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}