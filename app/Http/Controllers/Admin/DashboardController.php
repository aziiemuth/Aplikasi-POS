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
            'transaksi_hari_ini'   => Order::lunas()->whereDate('created_at', $today)->count(),
            'omzet_hari_ini'       => Order::lunas()->whereDate('created_at', $today)->sum('total_pembayaran'),

            // Bulan ini
            'transaksi_bulan_ini'  => Order::lunas()->where('created_at', '>=', $thisMonth)->count(),
            'omzet_bulan_ini'      => Order::lunas()->where('created_at', '>=', $thisMonth)->sum('total_pembayaran'),
        ];

        // Laba kotor hari ini
        $stats['laba_hari_ini'] = OrderItem::whereHas('order', fn($q) =>
            $q->lunas()->whereDate('created_at', $today)
        )->get()->sum('laba_item');

        // Laba kotor bulan ini
        $stats['laba_bulan_ini'] = OrderItem::whereHas('order', fn($q) =>
            $q->lunas()->where('created_at', '>=', $thisMonth)
        )->get()->sum('laba_item');

        // ── Grafik tren 7 hari terakhir ─────────────────────────
        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $trendData[] = [
                'label'     => Carbon::parse($day)->locale('id')->isoFormat('ddd D/M'),
                'omzet'     => (float) Order::lunas()->whereDate('created_at', $day)->sum('total_pembayaran'),
                'transaksi' => Order::lunas()->whereDate('created_at', $day)->count(),
            ];
        }

        // ── 5 Produk Terlaris bulan ini ─────────────────────────
        $produkTerlaris = OrderItem::selectRaw('nama_produk_snapshot, SUM(jumlah) as total_terjual, SUM(total_harga_item) as total_omzet')
            ->whereHas('order', fn($q) => $q->lunas()->where('created_at', '>=', $thisMonth))
            ->groupBy('nama_produk_snapshot')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        // ── 5 Stok Kritis ───────────────────────────────────────
        $stokKritis = Product::active()->lowStock()
            ->with('category')
            ->orderBy('stok_saat_ini')
            ->limit(5)
            ->get();

        // ── Log aktivitas terbaru ────────────────────────────────
        $recentLogs = ActivityLog::with('user')->latest()->limit(8)->get();

        return view('admin.dashboard', compact(
            'stats', 'trendData', 'produkTerlaris', 'stokKritis', 'recentLogs'
        ));
    }
}
