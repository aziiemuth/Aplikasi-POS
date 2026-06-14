@extends('layouts.app')

@section('title', 'Laporan Mutasi Stok')
@section('page-title', 'Laporan Mutasi Stok')
@section('page-subtitle', 'Riwayat lengkap pergerakan stok masuk dan keluar')

@section('content')
<div class="space-y-5 animate-fade-in">

    {{-- ===== FILTER PANEL ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.laporan.stok') }}">

            <div class="flex gap-2 mb-4 overflow-x-auto pb-1 -mx-1 px-1">
                @foreach(['harian' => 'Harian', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan', 'custom' => 'Custom'] as $val => $label)
                <button type="button" onclick="setMode('{{ $val }}')"
                    class="mode-btn px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold border transition-all whitespace-nowrap shrink-0
                        {{ request('mode', 'harian') === $val ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-slate-50 text-slate-600 border-slate-200 hover:border-emerald-300' }}">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            <input type="hidden" name="mode" id="mode-input" value="{{ request('mode', 'harian') }}">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div id="field-harian" class="{{ request('mode','harian') === 'harian' ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Pilih Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal', now()->toDateString()) }}"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-100 outline-none">
                </div>
                <div id="field-bulanan" class="{{ request('mode') === 'bulanan' ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Pilih Bulan</label>
                    <input type="month" name="bulan" value="{{ request('bulan', now()->format('Y-m')) }}"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-100 outline-none">
                </div>
                <div id="field-tahunan" class="{{ request('mode') === 'tahunan' ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Pilih Tahun</label>
                    <select name="tahun" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-100 outline-none">
                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ request('tahun', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div id="field-custom-dari" class="{{ request('mode') === 'custom' ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Dari Tanggal</label>
                    <input type="date" name="dari" value="{{ request('dari', now()->subDays(7)->toDateString()) }}"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-100 outline-none">
                </div>
                <div id="field-custom-sampai" class="{{ request('mode') === 'custom' ? '' : 'hidden' }}">
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="sampai" value="{{ request('sampai', now()->toDateString()) }}"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-100 outline-none">
                </div>
                <div class="flex gap-2 sm:col-span-2">
                    <button type="submit"
                        class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 sm:px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-sm">
                        <i class="fa-solid fa-magnifying-glass"></i> <span>Tampilkan</span>
                    </button>
                    <a href="{{ route('admin.laporan.stok.export', request()->query()) }}"
                        class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 sm:px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-sm">
                        <i class="fa-solid fa-file-excel"></i> <span>Export Excel</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- ===== RINGKASAN ===== --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-download text-emerald-600"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-500 font-medium">Stok Masuk</p>
                <p class="text-xl sm:text-2xl font-bold text-emerald-700">+{{ number_format($totalMasuk) }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $filterLabel }}</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 flex items-center gap-3 sm:gap-4">
            <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center shrink-0">
                <i class="fa-solid fa-upload text-rose-600"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-500 font-medium">Stok Keluar</p>
                <p class="text-xl sm:text-2xl font-bold text-rose-700">-{{ number_format($totalKeluar) }}</p>
                <p class="text-xs text-slate-400 truncate">{{ $filterLabel }}</p>
            </div>
        </div>
    </div>

    {{-- ===== TABEL MUTASI ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-4 sm:px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 min-w-0">
                <i class="fa-solid fa-clipboard-list text-emerald-500 shrink-0"></i>
                <h3 class="font-bold text-slate-800 text-sm truncate">Mutasi Stok — {{ $filterLabel }}</h3>
            </div>
            <span class="text-xs text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full font-medium shrink-0">{{ $mutations->total() }} data</span>
        </div>

        <div class="sm:hidden px-4 py-2 bg-slate-50 border-b border-slate-100 text-xs text-slate-400 flex items-center gap-1.5">
            <i class="fa-solid fa-arrows-left-right"></i> Geser ke kanan untuk melihat semua kolom
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[700px]">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left px-4 sm:px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                        <th class="text-left px-4 sm:px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Produk</th>
                        <th class="text-center px-4 sm:px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe</th>
                        <th class="text-center px-4 sm:px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Jml</th>
                        <th class="text-center px-4 sm:px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Sblm</th>
                        <th class="text-center px-4 sm:px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Ssdh</th>
                        <th class="text-left px-4 sm:px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Oleh</th>
                        <th class="text-left px-4 sm:px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Ket.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($mutations as $mut)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 sm:px-5 py-3 text-xs text-slate-600 whitespace-nowrap">
                            <p class="font-medium text-slate-700">{{ $mut->created_at->timezone('Asia/Jakarta')->format('d/m/Y') }}</p>
                            <p class="text-slate-400">{{ $mut->created_at->timezone('Asia/Jakarta')->format('H:i') }}</p>
                        </td>
                        <td class="px-4 sm:px-5 py-3">
                            <p class="font-semibold text-slate-800 text-xs">{{ $mut->product?->nama_produk ?? 'Dihapus' }}</p>
                            <p class="text-xs text-slate-400 font-mono">{{ $mut->product?->sku ?? '-' }}</p>
                        </td>
                        <td class="px-4 sm:px-5 py-3 text-center">
                            @if($mut->tipe === 'masuk')
                            <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-semibold bg-emerald-50 text-emerald-700">
                                <i class="fa-solid fa-arrow-down text-[9px]"></i> Masuk
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-semibold bg-rose-50 text-rose-700">
                                <i class="fa-solid fa-arrow-up text-[9px]"></i> Keluar
                            </span>
                            @endif
                        </td>
                        <td class="px-4 sm:px-5 py-3 text-center font-bold text-xs {{ $mut->tipe === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ $mut->tipe === 'masuk' ? '+' : '-' }}{{ $mut->jumlah }}
                        </td>
                        <td class="px-4 sm:px-5 py-3 text-center text-slate-600 text-xs">{{ $mut->stok_sebelum }}</td>
                        <td class="px-4 sm:px-5 py-3 text-center font-semibold text-slate-800 text-xs">{{ $mut->stok_sesudah }}</td>
                        <td class="px-4 sm:px-5 py-3 text-xs text-slate-600 whitespace-nowrap">{{ $mut->user?->name ?? 'Sistem' }}</td>
                        <td class="px-4 sm:px-5 py-3 text-xs text-slate-500 max-w-[120px] truncate">{{ $mut->keterangan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                            <i class="fa-solid fa-inbox text-slate-200 text-4xl mb-3 block"></i>
                            Tidak ada data mutasi pada periode ini
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($mutations->hasPages())
        <div class="px-4 sm:px-5 py-4 border-t border-slate-100">
            {{ $mutations->links() }}
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
        btn.className = btn.className.replace('bg-emerald-600 text-white border-emerald-600 shadow-sm', 'bg-slate-50 text-slate-600 border-slate-200 hover:border-emerald-300');
    });
    event.currentTarget.className = event.currentTarget.className.replace('bg-slate-50 text-slate-600 border-slate-200 hover:border-emerald-300', 'bg-emerald-600 text-white border-emerald-600 shadow-sm');
}
</script>
@endpush
