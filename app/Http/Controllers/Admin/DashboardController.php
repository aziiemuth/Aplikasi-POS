<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

/**
 * DashboardController — Fase 2.1: Dashboard Admin
 * Placeholder untuk Fase 7 (Laporan Bisnis)
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Data ringkasan untuk dashboard admin
        $stats = [
            'total_kasir'       => User::kasir()->active()->count(),
            'total_produk'      => Product::active()->count(),
            'produk_stok_tipis' => Product::active()->lowStock()->count(),
            'transaksi_hari_ini' => Order::lunas()
                ->whereDate('created_at', today())
                ->count(),
            'omzet_hari_ini'    => Order::lunas()
                ->whereDate('created_at', today())
                ->sum('total_pembayaran'),
        ];

        // Log aktivitas terbaru (10 terakhir)
        $recentLogs = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentLogs'));
    }
}
