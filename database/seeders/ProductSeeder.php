<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'nama' => 'Laptop Asus',
            'deskripsi' => 'Laptop Gaming Keren',
            'harga' => 10000000,
            'stok' => 50,
            'gambar' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400'
        ]);

        Product::create([
            'nama' => 'Mouse Gaming',
            'deskripsi' => 'Mouse RGB Keren',
            'harga' => 500000,
            'stok' => 45,
            'gambar' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=400'
        ]);

        Product::create([
            'nama' => 'Keyboard Mechanical',
            'deskripsi' => 'Keyboard Mechanical Terbaik',
            'harga' => 800000,
            'stok' => 35,
            'gambar' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=400'
        ]);

        Product::create([
            'nama' => 'Headset Gaming',
            'deskripsi' => 'Headset Gaming Suara Jernih',
            'harga' => 100000,
            'stok' => 20,
            'gambar' => 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400'
        ]);
    }
}
