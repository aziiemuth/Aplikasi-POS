@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan operasional bisnis hari ini')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- ===== STAT CARDS ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

        {{-- Kasir Aktif --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-surface-200 flex items-center gap-4 hover:shadow-md transition-shadow duration-200">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-user-tie text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="text-slate-500 text-xs font-medium">Kasir Aktif</p>
                <p class="text-2xl font-bold text-slate-800">{{ $stats['total_kasir'] }}</p>
                <p class="text-xs text-slate-400">Staf tersedia</p>
            </div>
        </div>

        {{-- Total Produk --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-surface-200 flex items-center gap-4 hover:shadow-md transition-shadow duration-200">
            <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-boxes-stacked text-violet-600 text-xl"></i>
            </div>
            <div>
                <p class="text-slate-500 text-xs font-medium">Total Produk</p>
                <p class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_produk']) }}</p>
                <p class="text-xs text-slate-400">Produk terdaftar</p>
            </div>
        </div>

        {{-- Stok Tipis --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border
            {{ $stats['produk_stok_tipis'] > 0 ? 'border-amber-200 bg-amber-50' : 'border-surface-200' }}
            flex items-center gap-4 hover:shadow-md transition-shadow duration-200">
            <div class="w-12 h-12 {{ $stats['produk_stok_tipis'] > 0 ? 'bg-amber-100' : 'bg-slate-100' }} rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-triangle-exclamation {{ $stats['produk_stok_tipis'] > 0 ? 'text-amber-500' : 'text-slate-400' }} text-xl"></i>
            </div>
            <div>
                <p class="text-slate-500 text-xs font-medium">Stok Tipis</p>
                <p class="text-2xl font-bold {{ $stats['produk_stok_tipis'] > 0 ? 'text-amber-600' : 'text-slate-800' }}">
                    {{ $stats['produk_stok_tipis'] }}
                </p>
                <p class="text-xs text-slate-400">Perlu restok segera</p>
            </div>
        </div>

        {{-- Omzet Hari Ini --}}
        <div class="rounded-2xl p-5 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow duration-200"
            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%)">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-money-bill-wave text-white text-xl"></i>
            </div>
            <div>
                <p class="text-emerald-100 text-xs font-medium">Omzet Hari Ini</p>
                <p class="text-xl font-bold text-white">
                    Rp {{ number_format($stats['omzet_hari_ini'], 0, ',', '.') }}
                </p>
                <p class="text-emerald-200 text-xs">{{ $stats['transaksi_hari_ini'] }} transaksi</p>
            </div>
        </div>
    </div>

    {{-- ===== BOTTOM SECTION ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Log Aktivitas Terbaru --}}
        <div class="bg-white rounded-2xl shadow-sm border border-surface-200">
            <div class="flex items-center justify-between px-6 py-4 border-b border-surface-100">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-primary-500"></i>
                    <h3 class="font-semibold text-slate-800 text-sm">Aktivitas Terbaru</h3>
                </div>
                <span class="text-xs text-slate-400 bg-slate-100 px-2 py-1 rounded-full">10 terakhir</span>
            </div>

            <div class="divide-y divide-slate-50 max-h-72 overflow-y-auto">
                @forelse($recentLogs as $log)
                <div class="flex items-start gap-3 px-6 py-3 hover:bg-slate-50 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center shrink-0 text-xs font-bold text-slate-500 mt-0.5">
                        {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-700 truncate leading-relaxed">{{ $log->aksi }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $log->user?->username ?? 'Sistem' }}</p>
                    </div>
                    <p class="text-xs text-slate-400 shrink-0">
                        {{ $log->created_at->diffForHumans() }}
                    </p>
                </div>
                @empty
                <div class="px-6 py-8 text-center">
                    <i class="fa-solid fa-inbox text-slate-200 text-3xl mb-2"></i>
                    <p class="text-slate-400 text-sm">Belum ada aktivitas</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Panduan Akun --}}
        <div class="bg-white rounded-2xl shadow-sm border border-surface-200">
            <div class="flex items-center gap-2 px-6 py-4 border-b border-surface-100">
                <i class="fa-solid fa-circle-info text-primary-500"></i>
                <h3 class="font-semibold text-slate-800 text-sm">Panduan Sistem</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-shield-halved text-amber-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-700">Role Admin</p>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Akses penuh ke semua fitur: Master Data, Stok, Supplier, Laporan, dan Manajemen User.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-tie text-emerald-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-700">Role Kasir</p>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Hanya bisa mengakses layar POS untuk transaksi. Tidak bisa mengubah stok manual atau melihat laporan.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-ban text-red-600 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-700">Anti-Brute Force</p>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">Login dibatasi 5x percobaan gagal. IP akan diblokir otomatis selama 5 menit jika melebihi batas.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
