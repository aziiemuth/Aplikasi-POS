@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan operasional bisnis Anda')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="space-y-5 animate-fade-in">

    {{-- ===== ROW 1: STAT CARDS (2 kolom di mobile, 4 di desktop) ===== --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">

        {{-- Omzet Hari Ini --}}
        <div class="rounded-2xl p-4 sm:p-5 shadow-sm flex items-center gap-3 sm:gap-4"
            style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-money-bill-wave text-white text-base sm:text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-blue-100 text-[11px] sm:text-xs font-medium">Omzet Hari Ini</p>
                <p class="text-sm sm:text-xl font-bold text-white truncate">Rp {{ number_format($stats['omzet_hari_ini'], 0, ',', '.') }}</p>
                <p class="text-blue-200 text-[10px] sm:text-xs">{{ $stats['transaksi_hari_ini'] }} transaksi</p>
            </div>
        </div>

        {{-- Omzet Bulan Ini --}}
        <div class="rounded-2xl p-4 sm:p-5 shadow-sm flex items-center gap-3 sm:gap-4"
            style="background: linear-gradient(135deg, #059669 0%, #047857 100%)">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-chart-line text-white text-base sm:text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-emerald-100 text-[11px] sm:text-xs font-medium">Omzet Bulan Ini</p>
                <p class="text-sm sm:text-xl font-bold text-white truncate">Rp {{ number_format($stats['omzet_bulan_ini'], 0, ',', '.') }}</p>
                <p class="text-emerald-200 text-[10px] sm:text-xs">{{ $stats['transaksi_bulan_ini'] }} transaksi</p>
            </div>
        </div>

        {{-- Laba Kotor Hari Ini --}}
        <div class="rounded-2xl p-4 sm:p-5 shadow-sm flex items-center gap-3 sm:gap-4"
            style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%)">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-coins text-white text-base sm:text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-violet-100 text-[11px] sm:text-xs font-medium">Laba Hari Ini</p>
                <p class="text-sm sm:text-xl font-bold text-white truncate">Rp {{ number_format($stats['laba_hari_ini'], 0, ',', '.') }}</p>
                <p class="text-violet-200 text-[10px] sm:text-xs">setelah HPP & diskon</p>
            </div>
        </div>

        {{-- Stok Tipis --}}
        <div class="rounded-2xl p-4 sm:p-5 shadow-sm flex items-center gap-3 sm:gap-4 border
            {{ $stats['produk_stok_tipis'] > 0 ? 'border-amber-200 bg-amber-50' : 'border-slate-200 bg-white' }}">
            <div class="w-10 h-10 sm:w-12 sm:h-12 {{ $stats['produk_stok_tipis'] > 0 ? 'bg-amber-100' : 'bg-slate-100' }} rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-triangle-exclamation {{ $stats['produk_stok_tipis'] > 0 ? 'text-amber-500' : 'text-slate-400' }} text-base sm:text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-slate-500 text-[11px] sm:text-xs font-medium">Stok Tipis</p>
                <p class="text-2xl sm:text-2xl font-bold {{ $stats['produk_stok_tipis'] > 0 ? 'text-amber-600' : 'text-slate-800' }}">{{ $stats['produk_stok_tipis'] }}</p>
                <p class="text-slate-400 text-[10px] sm:text-xs">perlu restok</p>
            </div>
        </div>
    </div>

    {{-- ===== ROW 2: GRAFIK TREN + PRODUK TERLARIS ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Grafik Tren 7 Hari --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-5">
            <div class="flex items-start justify-between mb-4 gap-2">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm sm:text-base">Tren Penjualan</h3>
                    <p class="text-xs text-slate-400 mt-0.5">7 hari terakhir</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs text-slate-500 shrink-0">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> Omzet</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-emerald-400 inline-block"></span> Transaksi</span>
                </div>
            </div>
            <div class="relative" style="height: 200px; min-height: 160px;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- 5 Produk Terlaris --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
            <div class="px-4 sm:px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-trophy text-amber-500"></i>
                <h3 class="font-bold text-slate-800 text-sm">Produk Terlaris</h3>
                <span class="ml-auto text-xs text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full shrink-0">Bulan ini</span>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($produkTerlaris as $index => $item)
                <div class="flex items-center gap-3 px-4 sm:px-5 py-3">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                        {{ $index === 0 ? 'bg-amber-100 text-amber-600' : ($index === 1 ? 'bg-slate-100 text-slate-600' : ($index === 2 ? 'bg-orange-100 text-orange-600' : 'bg-slate-50 text-slate-400')) }}">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-semibold text-slate-700 truncate">{{ $item->nama_produk_snapshot }}</p>
                        <p class="text-xs text-slate-400">{{ $item->total_terjual }} terjual</p>
                    </div>
                    <p class="text-xs font-bold text-slate-600 shrink-0">Rp {{ number_format($item->total_omzet, 0, ',', '.') }}</p>
                </div>
                @empty
                <div class="px-5 py-8 text-center">
                    <i class="fa-solid fa-box-open text-slate-200 text-3xl mb-2"></i>
                    <p class="text-slate-400 text-sm">Belum ada data penjualan bulan ini</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ===== ROW 3: STOK KRITIS + LOG AKTIVITAS ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Stok Kritis --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
            <div class="px-4 sm:px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                <h3 class="font-bold text-slate-800 text-sm">Peringatan Stok Kritis</h3>
                @if($stats['produk_stok_tipis'] > 0)
                <a href="{{ route('admin.products.index', ['stok' => 'tipis']) }}"
                   class="ml-auto text-xs text-blue-600 hover:underline font-medium shrink-0">Lihat semua</a>
                @endif
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($stokKritis as $produk)
                <div class="flex items-center gap-3 px-4 sm:px-5 py-3">
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-rose-50 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-boxes-stacked text-rose-400 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-semibold text-slate-700 truncate">{{ $produk->nama_produk }}</p>
                        <p class="text-xs text-slate-400">Min: {{ $produk->stok_minimum }} {{ $produk->satuan }}</p>
                    </div>
                    <span class="text-xs sm:text-sm font-bold {{ $produk->stok_saat_ini <= 0 ? 'text-rose-600 bg-rose-50' : 'text-amber-600 bg-amber-50' }} px-2 py-0.5 rounded-lg shrink-0">
                        {{ $produk->stok_saat_ini }} {{ $produk->satuan }}
                    </span>
                </div>
                @empty
                <div class="px-5 py-8 text-center">
                    <i class="fa-solid fa-check-circle text-emerald-300 text-3xl mb-2"></i>
                    <p class="text-slate-400 text-sm">Semua stok dalam kondisi aman</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Log Aktivitas Terbaru --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center justify-between px-4 sm:px-5 py-4 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-primary-500"></i>
                    <h3 class="font-bold text-slate-800 text-sm">Aktivitas Terbaru</h3>
                </div>
                <a href="{{ route('admin.laporan.activity-log') }}" class="text-xs text-blue-600 hover:underline font-medium shrink-0">Lihat semua</a>
            </div>
            <div class="divide-y divide-slate-50 max-h-64 overflow-y-auto">
                @forelse($recentLogs as $log)
                <div class="flex items-start gap-3 px-4 sm:px-5 py-3 hover:bg-slate-50 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center shrink-0 text-xs font-bold text-slate-500 mt-0.5">
                        {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs sm:text-sm font-medium text-slate-700 truncate">{{ $log->aksi }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $log->user?->username ?? 'Sistem' }}</p>
                    </div>
                    <p class="text-xs text-slate-400 shrink-0">{{ $log->created_at->diffForHumans() }}</p>
                </div>
                @empty
                <div class="px-5 py-8 text-center">
                    <i class="fa-solid fa-inbox text-slate-200 text-3xl mb-2"></i>
                    <p class="text-slate-400 text-sm">Belum ada aktivitas</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function() {
    const trendData = @json($trendData);
    const ctx = document.getElementById('trendChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: trendData.map(d => d.label),
            datasets: [
                {
                    label: 'Omzet (Rp)',
                    data: trendData.map(d => d.omzet),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointBackgroundColor: '#3b82f6',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y',
                },
                {
                    label: 'Transaksi',
                    data: trendData.map(d => d.transaksi),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16,185,129,0.06)',
                    borderWidth: 2,
                    pointRadius: 3,
                    pointBackgroundColor: '#10b981',
                    tension: 0.4,
                    fill: false,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            if (ctx.datasetIndex === 0) {
                                return ' Omzet: Rp ' + ctx.raw.toLocaleString('id-ID');
                            }
                            return ' Transaksi: ' + ctx.raw;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: { font: { size: 10 }, color: '#94a3b8', maxRotation: 0 }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        font: { size: 10 }, color: '#94a3b8',
                        callback: v => v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : v >= 1000 ? (v/1000).toFixed(0)+'rb' : v
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false },
                    ticks: { font: { size: 10 }, color: '#10b981', stepSize: 1 }
                }
            }
        }
    });
})();
</script>
@endpush
