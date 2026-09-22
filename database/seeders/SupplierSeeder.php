<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('suppliers')->insert([
            [
                'name' => 'PT Indofood Sukses Makmur',
                'phone' => '021-57958822',
                'address' => 'Jakarta Selatan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT Unilever Indonesia Tbk',
                'phone' => '021-80827000',
                'address' => 'Tangerang, Banten',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PT Wings Surya',
                'phone' => '031-8533535',
                'address' => 'Surabaya, Jawa Timur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}