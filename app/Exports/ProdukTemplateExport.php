<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * Template Excel kosong untuk panduan import produk massal.
 */
class ProdukTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    public function array(): array
    {
        // Contoh data agar Admin tahu formatnya
        return [
            ['Produk Contoh A', 'SKU-001', 'Makanan', 'pcs', 5000, 8000, 50, 5, 'Contoh deskripsi produk'],
            ['Produk Contoh B', 'SKU-002', 'Minuman', 'botol', 3000, 5000, 100, 10, ''],
        ];
    }

    public function headings(): array
    {
        return [
            'nama_produk',
            'sku',
            'kategori',
            'satuan',
            'modal_hpp',
            'harga_jual',
            'stok_saat_ini',
            'stok_minimum',
            'deskripsi',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF16A34A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Template Import Produk';
    }
}
