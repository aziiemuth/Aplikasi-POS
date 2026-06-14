<?php

namespace App\Exports;

use App\Models\Order;
use App\Models\OrderItem;
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

class LaporanPenjualanExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle, WithMapping
{
    public function __construct(
        private Carbon $startDate,
        private Carbon $endDate
    ) {}

    public function collection()
    {
        return Order::lunas()
            ->with(['user', 'items'])
            ->whereBetween('created_at', [
                $this->startDate->copy()->startOfDay(),
                $this->endDate->copy()->endOfDay(),
            ])
            ->latest()
            ->get();
    }

    public function map($order): array
    {
        $labaKotor = $order->items->sum(fn($item) =>
            ($item->harga_jual_snapshot - $item->diskon_item - $item->hpp_snapshot) * $item->jumlah
        ) - $order->diskon_global;

        return [
            $order->nomor_order,
            $order->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i'),
            $order->user?->name ?? '-',
            $order->nama_customer,
            $order->metode_pembayaran,
            $order->total_sebelum_diskon,
            $order->diskon_global,
            $order->pajak_ppn,
            $order->total_pembayaran,
            $order->jumlah_bayar,
            $order->kembalian,
            $labaKotor,
        ];
    }

    public function headings(): array
    {
        return [
            'No. Order',
            'Tanggal & Jam',
            'Kasir',
            'Customer',
            'Metode Bayar',
            'Subtotal (Rp)',
            'Diskon Global (Rp)',
            'Pajak/PPN (Rp)',
            'Total Bayar (Rp)',
            'Uang Diterima (Rp)',
            'Kembalian (Rp)',
            'Laba Kotor (Rp)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }
}
