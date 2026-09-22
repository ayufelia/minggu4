<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil semua data kategori
        $categories = DB::table('categories')->get();

        // Mengambil semua data produk beserta nama kategorinya
        $products = DB::table('products')
            ->join(
                'categories',
                'products.category_id',
                '=',
                'categories.id'
            )
            ->select(
                'products.*',
                'categories.name as category_name'
            )
            ->get();

        // Mengambil semua data supplier
        $suppliers = DB::table('suppliers')->get();

        // Mengirim data ke halaman dashboard
        return view('dashboard', compact(
            'categories',
            'products',
            'suppliers'
        ));
    }
}