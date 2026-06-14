<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\StockMutation;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * LaporanController — Fase 7
 * Mengelola halaman Laporan Penjualan, Laporan Stok, dan Log Aktivitas.
 */
class LaporanController extends Controller
{
    // =========================================================
    // 7.2a: Laporan Penjualan
    // =========================================================

    public function penjualan(Request $request)
    {
        [$startDate, $endDate, $filterLabel] = $this->resolveFilter($request);

        $query = Order::lunas()
            ->with('user')
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()])
            ->latest();

        $orders = $query->paginate(25)->withQueryString();

        // Ringkasan periode
        $summaryQuery = Order::lunas()
            ->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);

        $totalOmzet      = $summaryQuery->sum('total_pembayaran');
        $totalTransaksi  = $summaryQuery->count();

        // Laba kotor: hitung dari order_items
        $labaKotor = \App\Models\OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->lunas()->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()]);
        })->get()->sum('laba_item');

        $kasirList = User::kasir()->orderBy('name')->get();

        return view('admin.laporan.penjualan', compact(
            'orders', 'totalOmzet', 'totalTransaksi', 'labaKotor',
            'filterLabel', 'startDate', 'endDate', 'kasirList'
        ));
    }

    public function exportPenjualan(Request $request)
    {
        [$startDate, $endDate, $filterLabel] = $this->resolveFilter($request);

        $fileName = 'laporan-penjualan-' . $startDate->format('Y-m-d') . '_sd_' . $endDate->format('Y-m-d') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaporanPenjualanExport($startDate, $endDate),
            $fileName
        );
    }

    // =========================================================
    // 7.2b: Laporan Mutasi Stok
    // =========================================================

    public function stok(Request $request)
    {
        [$startDate, $endDate, $filterLabel] = $this->resolveFilter($request);

        $mutations = StockMutation::with(['product', 'user', 'supplier'])
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->copy()->endOfDay()])
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $totalMasuk  = StockMutation::masuk()->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])->sum('jumlah');
        $totalKeluar = StockMutation::keluar()->whereBetween('created_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])->sum('jumlah');

        return view('admin.laporan.stok', compact(
            'mutations', 'totalMasuk', 'totalKeluar',
            'filterLabel', 'startDate', 'endDate'
        ));
    }

    public function exportStok(Request $request)
    {
        [$startDate, $endDate, $filterLabel] = $this->resolveFilter($request);

        $fileName = 'laporan-stok-' . $startDate->format('Y-m-d') . '_sd_' . $endDate->format('Y-m-d') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaporanStokExport($startDate, $endDate),
            $fileName
        );
    }

    // =========================================================
    // 7.4: Log Aktivitas (Audit Trail)
    // =========================================================

    public function activityLog(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('aksi')) {
            $query->where('aksi', 'like', '%' . $request->aksi . '%');
        }
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $logs     = $query->paginate(25)->withQueryString();
        $userList = User::orderBy('name')->get();

        return view('admin.laporan.activity-log', compact('logs', 'userList'));
    }

    // =========================================================
    // Helper: Resolve filter tanggal dari request
    // =========================================================

    private function resolveFilter(Request $request): array
    {
        $mode = $request->input('mode', 'harian');

        switch ($mode) {
            case 'bulanan':
                $bulan = $request->input('bulan', now()->format('Y-m'));
                $start = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
                $end   = $start->copy()->endOfMonth();
                $label = 'Bulan ' . $start->translatedFormat('F Y');
                break;

            case 'tahunan':
                $tahun = (int) $request->input('tahun', now()->year);
                $start = Carbon::create($tahun)->startOfYear();
                $end   = $start->copy()->endOfYear();
                $label = 'Tahun ' . $tahun;
                break;

            case 'custom':
                $start = Carbon::parse($request->input('dari', now()->subDays(7)->toDateString()));
                $end   = Carbon::parse($request->input('sampai', now()->toDateString()));
                $label = $start->format('d/m/Y') . ' — ' . $end->format('d/m/Y');
                break;

            default: // harian
                $tanggal = $request->input('tanggal', now()->toDateString());
                $start   = Carbon::parse($tanggal);
                $end     = $start->copy();
                $label   = 'Tanggal ' . $start->translatedFormat('d F Y');
                break;
        }

        return [$start, $end, $label];
    }
}
