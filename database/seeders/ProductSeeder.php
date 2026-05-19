<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Product::create([
            'title' => 'Laptop Gaming',
            'price' => 15000000,
            'stock' => 10,
            'image' => 'laptop.jpg',
        ]);

        \App\Models\Product::create([
            'title' => 'Mouse Wireless',
            'price' => 150000,
            'stock' => 50,
            'image' => 'mouse.jpg',
        ]);

        \App\Models\Product::create([
            'title' => 'Keyboard Mechanical',
            'price' => 500000,
            'stock' => 30,
            'image' => 'keyboard.jpg',
        ]);
    }
}
