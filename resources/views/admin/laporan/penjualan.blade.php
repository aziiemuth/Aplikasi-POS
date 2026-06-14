@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('page-title', 'Laporan Penjualan')
@section('page-subtitle', 'Filter, analisis, dan export data transaksi penjualan')

@section('content')
<div class="space-y-5 animate-fade-in">

    {{-- ===== FILTER PANEL ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.laporan.penjualan') }}" id="filter-form">

            {{-- Tab Mode — scroll horizontal di mobile --}}
            <div class="flex gap-2 mb-4 overflow-x-auto pb-1 -mx-1 px-1">
                @foreach(['harian' => 'Harian', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan', 'custom' => 'Custom'] as $val => $label)
                <button type="button" onclick="setMode('{{ $val }}')"
                    class="mode-btn px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold border transition-all whitespace-nowrap shrink-0
                        {{ request('mode', 'harian') === $val ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-slate-50 text-slate-600 border-slate-200 hover:border-blue-300' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            <input type="hidden" name="mode" id="mode-input" value="{{ request('mode', 'harian') }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                {{-- Field inputs --}}
                <div id="field-harian" class="{{ request('mode','harian') === 'harian' ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Pilih Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal', now()->toDateString()) }}"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                </div>

                <div id="field-bulanan" class="{{ request('mode') === 'bulanan' ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Pilih Bulan</label>
                    <input type="month" name="bulan" value="{{ request('bulan', now()->format('Y-m')) }}"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                </div>

                <div id="field-tahunan" class="{{ request('mode') === 'tahunan' ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Pilih Tahun</label>
                    <select name="tahun" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ request('tahun', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div id="field-custom-dari" class="{{ request('mode') === 'custom' ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ request('dari', now()->subDays(7)->toDateString()) }}"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                </div>
                <div id="field-custom-sampai" class="{{ request('mode') === 'custom' ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ request('sampai', now()->toDateString()) }}"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                </div>

                {{-- Tombol aksi --}}
                <div class="flex gap-2 sm:col-span-2">
                    <button type="submit"
                        class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 sm:px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-sm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Tampilkan</span>
                    </button>
                    <a href="{{ route('admin.laporan.penjualan.export', request()->query()) }}"
                        class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 sm:px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-sm">
                        <i class="fa-solid fa-file-excel"></i>
                        <span>Export Excel</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ===== RINGKASAN PERIODE ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-receipt text-blue-600"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-500 font-medium">Total Transaksi</p>
                <p class="text-2xl font-bold text-slate-800">{{ $totalTransaksi }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $filterLabel }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-money-bill-wave text-emerald-600"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-500 font-medium">Total Omzet</p>
                <p class="text-xl font-bold text-slate-800 truncate">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $filterLabel }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-4">
            <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-coins text-violet-600"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-500 font-medium">Laba Kotor</p>
                <p class="text-xl font-bold text-slate-800 truncate">Rp {{ number_format($labaKotor, 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400">setelah HPP & diskon</p>
            </div>
        </div>
    </div>

    {{-- ===== TABEL TRANSAKSI ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-4 sm:px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 min-w-0">
                <i class="fa-solid fa-table-list text-primary-500 shrink-0"></i>
                <h3 class="font-bold text-slate-800 text-sm truncate">Transaksi — {{ $filterLabel }}</h3>
            </div>
            <span class="text-xs text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full font-medium shrink-0">{{ $orders->total() }} data</span>
        </div>

        {{-- Hint scroll di mobile --}}
        <div class="sm:hidden px-4 py-2 bg-slate-50 border-b border-slate-100 text-xs text-slate-400 flex items-center gap-1.5">
            <i class="fa-solid fa-arrows-left-right"></i> Geser ke kanan untuk melihat semua kolom
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[600px]">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left px-4 sm:px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">No. Order</th>
                        <th class="text-left px-4 sm:px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                        <th class="text-left px-4 sm:px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Kasir</th>
                        <th class="text-left px-4 sm:px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Customer</th>
                        <th class="text-left px-4 sm:px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Metode</th>
                        <th class="text-right px-4 sm:px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Total</th>
                        <th class="text-right px-4 sm:px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Laba</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 sm:px-5 py-3">
                            <span class="font-mono text-xs font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-lg whitespace-nowrap">{{ $order->nomor_order }}</span>
                        </td>
                        <td class="px-4 sm:px-5 py-3 text-slate-600 whitespace-nowrap text-xs">
                            {{ $order->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 sm:px-5 py-3 text-slate-700 font-medium text-xs whitespace-nowrap">{{ $order->user?->name ?? '-' }}</td>
                        <td class="px-4 sm:px-5 py-3 text-slate-600 text-xs max-w-[100px] truncate">{{ $order->nama_customer }}</td>
                        <td class="px-4 sm:px-5 py-3">
                            @php
                                $metodeColor = match(true) {
                                    str_contains($order->metode_pembayaran, 'Tunai') => 'emerald',
                                    str_contains($order->metode_pembayaran, 'QRIS')  => 'blue',
                                    default => 'slate',
                                };
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium whitespace-nowrap bg-{{ $metodeColor }}-50 text-{{ $metodeColor }}-700">
                                {{ $order->metode_pembayaran }}
                            </span>
                        </td>
                        <td class="px-4 sm:px-5 py-3 text-right font-bold text-slate-800 whitespace-nowrap text-xs">
                            Rp {{ number_format($order->total_pembayaran, 0, ',', '.') }}
                        </td>
                        <td class="px-4 sm:px-5 py-3 text-right font-semibold whitespace-nowrap text-xs">
                            @php $laba = $order->laba_kotor; @endphp
                            <span class="{{ $laba >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                Rp {{ number_format($laba, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                            <i class="fa-solid fa-inbox text-slate-200 text-4xl mb-3 block"></i>
                            Tidak ada transaksi pada periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="px-4 sm:px-5 py-4 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function setMode(mode) {
    document.getElementById('mode-input').value = mode;
    ['harian','bulanan','tahunan','custom-dari','custom-sampai'].forEach(f => {
        const el = document.getElementById('field-' + f);
        if (el) el.classList.add('hidden');
    });
    if (mode === 'custom') {
        document.getElementById('field-custom-dari')?.classList.remove('hidden');
        document.getElementById('field-custom-sampai')?.classList.remove('hidden');
    } else {
        document.getElementById('field-' + mode)?.classList.remove('hidden');
    }
    document.querySelectorAll('.mode-btn').forEach(btn => {
        btn.className = btn.className
            .replace('bg-blue-600 text-white border-blue-600 shadow-sm', 'bg-slate-50 text-slate-600 border-slate-200 hover:border-blue-300');
    });
    event.currentTarget.className = event.currentTarget.className
        .replace('bg-slate-50 text-slate-600 border-slate-200 hover:border-blue-300', 'bg-blue-600 text-white border-blue-600 shadow-sm');
}
</script>
@endpush
