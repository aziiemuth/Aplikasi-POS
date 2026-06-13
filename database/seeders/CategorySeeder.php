<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed kategori produk dummy untuk demo awal.
     */
    public function run(): void
    {
        $categories = [
            ['nama_kategori' => 'Minuman Dingin',    'deskripsi' => 'Air mineral, jus, minuman kaleng, dll', 'icon' => 'fa-solid fa-wine-bottle', 'is_active' => true],
            ['nama_kategori' => 'Minuman Panas',     'deskripsi' => 'Kopi, teh, dan minuman panas lainnya', 'icon' => 'fa-solid fa-mug-hot',     'is_active' => true],
            ['nama_kategori' => 'Makanan Ringan',    'deskripsi' => 'Snack, keripik, biskuit, dll',         'icon' => 'fa-solid fa-cookie',      'is_active' => true],
            ['nama_kategori' => 'Makanan Berat',     'deskripsi' => 'Nasi, mie, dan makanan mengenyangkan', 'icon' => 'fa-solid fa-utensils',    'is_active' => true],
            ['nama_kategori' => 'Rokok & Tembakau',  'deskripsi' => 'Rokok, korek, dan produk tembakau',   'icon' => 'fa-solid fa-smoking',     'is_active' => true],
            ['nama_kategori' => 'Toiletries',        'deskripsi' => 'Sabun, shampoo, pasta gigi, dll',     'icon' => 'fa-solid fa-soap',        'is_active' => true],
            ['nama_kategori' => 'Sembako',           'deskripsi' => 'Beras, gula, minyak goreng, dll',     'icon' => 'fa-solid fa-box',         'is_active' => true],
            ['nama_kategori' => 'Elektronik',        'deskripsi' => 'Baterai, charger, kabel, dll',        'icon' => 'fa-solid fa-plug',        'is_active' => true],
            ['nama_kategori' => 'Lainnya',           'deskripsi' => 'Produk yang tidak termasuk kategori di atas', 'icon' => 'fa-solid fa-tag', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['nama_kategori' => $category['nama_kategori']],
                $category
            );
        }

        $this->command->info('✅ CategorySeeder: ' . count($categories) . ' kategori berhasil dibuat.');
    }
}
