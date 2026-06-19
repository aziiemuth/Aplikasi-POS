<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * DashboardController — Fase 7.1: Dashboard Admin dengan Tren Penjualan
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today     = today();
        $thisMonth = now()->startOfMonth();

        // ── Stat cards ──────────────────────────────────────────
        $stats = [
            'total_kasir'          => User::kasir()->active()->count(),
            'total_produk'         => Product::active()->count(),
            'produk_stok_tipis'    => Product::active()->lowStock()->count(),

            // Hari ini
            'transaksi_hari_ini'   => Order::lunas()->onDate($today)->count(),
            'omzet_hari_ini'       => Order::lunas()->onDate($today)->sum('total_pembayaran'),

            // Bulan ini
            'transaksi_bulan_ini'  => Order::lunas()->sinceDate($thisMonth)->count(),
            'omzet_bulan_ini'      => Order::lunas()->sinceDate($thisMonth)->sum('total_pembayaran'),
        ];

        // Laba kotor hari ini
        $stats['laba_hari_ini'] = Order::lunas()->onDate($today)->get()->sum(fn($order) => $order->laba_kotor);

        // Laba kotor bulan ini
        $stats['laba_bulan_ini'] = Order::lunas()->sinceDate($thisMonth)->get()->sum(fn($order) => $order->laba_kotor);

        // ── Grafik tren 7 hari terakhir ─────────────────────────
        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $trendData[] = [
                'label'     => Carbon::parse($day)->locale('id')->isoFormat('ddd D/M'),
                'omzet'     => (float) Order::lunas()->onDate($day)->sum('total_pembayaran'),
                'transaksi' => Order::lunas()->onDate($day)->count(),
            ];
        }

        // ── 5 Produk Terlaris bulan ini ─────────────────────────
        $produkTerlaris = OrderItem::selectRaw('nama_produk_snapshot, SUM(jumlah) as total_terjual, SUM(total_harga_item) as total_omzet')
            ->whereHas('order', fn($q) => $q->lunas()->sinceDate($thisMonth))
            ->groupBy('nama_produk_snapshot')
            ->orderByRaw('total_terjual DESC')
            ->limit(5)
            ->get();

        // ── 5 Stok Kritis ───────────────────────────────────────
        $stokKritis = Product::active()->lowStock()
            ->with('category')
            ->orderByStock()
            ->limit(5)
            ->get();

        // ── Log aktivitas terbaru ────────────────────────────────
        $recentLogs = ActivityLog::with('user')->latest()->limit(8)->get();

        return view('admin.dashboard', compact(
            'stats', 'trendData', 'produkTerlaris', 'stokKritis', 'recentLogs'
        ));
    }
}
