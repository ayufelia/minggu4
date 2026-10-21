<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{

    // Menampilkan semua produk
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

    // Menampilkan halaman tambah produk
    public function create()
    {
        $categories = DB::table('categories')->get();

        return view('products.create', compact('categories'));
    }

    // Menyimpan produk baru beserta gambar
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $manager = new ImageManager(new Driver());

            $image = $manager->read(
                $request->file('image')->getRealPath()
            );

            $image->cover(800, 800);

            $filename = uniqid('produk_') . '.jpg';
            $imagePath = 'products/' . $filename;

            Storage::disk('public')->put(
                $imagePath,
                $image->toJpeg(85)->toString()
            );
        }

        DB::table('products')->insert([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'sku' => $request->sku,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
        ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    // Menampilkan halaman edit produk
    public function edit($id)
    {
        $product = DB::table('products')
            ->where('id', $id)
            ->first();

        if (!$product) {
            abort(404);
        }

        $categories = DB::table('categories')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    // Memperbarui produk dan gambar
    public function update(Request $request, $id)
    {
        $product = DB::table('products')
            ->where('id', $id)
            ->first();

        if (!$product) {
            abort(404);
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = $product->image;

        if ($request->hasFile('image')) {
            $manager = new ImageManager(new Driver());

            $image = $manager->read(
                $request->file('image')->getRealPath()
            );

            $image->cover(800, 800);

            $filename = uniqid('produk_') . '.jpg';
            $newImagePath = 'products/' . $filename;

            Storage::disk('public')->put(
                $newImagePath,
                $image->toJpeg(85)->toString()
            );

            if (!empty($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $imagePath = $newImagePath;
        }

        DB::table('products')
            ->where('id', $id)
            ->update([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'sku' => $request->sku,
                'price' => $request->price,
                'stock' => $request->stock,
                'image' => $imagePath,
            ]);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    // Menghapus produk beserta gambarnya
    public function destroy($id)
    {
        $product = DB::table('products')
            ->where('id', $id)
            ->first();

        if (!$product) {
            abort(404);
        }

        if (!empty($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        DB::table('products')
            ->where('id', $id)
            ->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}