<?php

namespace App\Exports;

use App\Models\StockMutation;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LaporanStokExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle, WithMapping
{
    public function __construct(
        private Carbon $startDate,
        private Carbon $endDate
    ) {}

    public function collection()
    {
        return StockMutation::with(['product', 'user', 'supplier'])
            ->whereBetween('created_at', [
                $this->startDate->copy()->startOfDay(),
                $this->endDate->copy()->endOfDay(),
            ])
            ->latest()
            ->get();
    }

    public function map($mutation): array
    {
        return [
            $mutation->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
            $mutation->product?->nama_produk ?? '-',
            $mutation->product?->sku ?? '-',
            strtoupper($mutation->tipe),
            $mutation->jumlah,
            $mutation->stok_sebelum,
            $mutation->stok_sesudah,
            $mutation->harga_beli > 0 ? $mutation->harga_beli : '-',
            $mutation->supplier?->nama_supplier ?? '-',
            $mutation->user?->name ?? 'Sistem',
            $mutation->keterangan ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'Tanggal & Jam',
            'Nama Produk',
            'SKU',
            'Tipe',
            'Jumlah',
            'Stok Sebelum',
            'Stok Sesudah',
            'Harga Beli (Rp)',
            'Supplier',
            'Oleh (User)',
            'Keterangan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF059669']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Mutasi Stok';
    }
}
