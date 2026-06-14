<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use App\Services\CacheService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class ProdukImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private int $rowCount = 0;

    public function collection(Collection $rows): void
    {
        $categoryCache = [];

        foreach ($rows as $row) {
            // Lewati baris yang nama_produknya kosong
            if (empty($row['nama_produk'])) {
                continue;
            }

            // Resolve category
            $categoryId = null;
            $katNama = trim($row['kategori'] ?? '');
            if ($katNama) {
                if (!isset($categoryCache[$katNama])) {
                    $cat = Category::firstOrCreate(
                        ['nama_kategori' => $katNama],
                        ['deskripsi' => '']
                    );
                    $categoryCache[$katNama] = $cat->id;
                }
                $categoryId = $categoryCache[$katNama];
            }

            // Generate SKU jika kosong
            $sku = trim($row['sku'] ?? '');
            if (!$sku) {
                $sku = strtoupper(Str::random(4)) . '-' . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            }

            // Buat atau perbarui produk berdasarkan SKU
            Product::updateOrCreate(
                ['sku' => $sku],
                [
                    'category_id'   => $categoryId,
                    'nama_produk'   => trim($row['nama_produk']),
                    'deskripsi'     => trim($row['deskripsi'] ?? ''),
                    'satuan'        => trim($row['satuan'] ?? 'pcs'),
                    'modal_hpp'     => (float) ($row['modal_hpp'] ?? 0),
                    'harga_jual'    => (float) ($row['harga_jual'] ?? 0),
                    'stok_saat_ini' => (int)   ($row['stok_saat_ini'] ?? 0),
                    'stok_minimum'  => (int)   ($row['stok_minimum'] ?? 0),
                    'is_active'     => 1,
                ]
            );

            $this->rowCount++;
        }

        // Invalidate cache produk
        CacheService::forgetProducts();
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }
}
