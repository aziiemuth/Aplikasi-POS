@extends('layouts.app')
@section('title', 'Detail Produk: ' . $product->nama_produk)
@section('page-title', $product->nama_produk)
@section('page-subtitle', 'SKU: ' . $product->sku . ' · ' . ($product->category?->nama_kategori ?? 'Tanpa Kategori'))

@push('styles')
<style>
@media print {
    body * { visibility: hidden; }
    #print-area, #print-area * { visibility: visible; }
    #print-area { position:fixed; top:0; left:0; width:100%; }
}
</style>
@endpush

@section('content')
<div class="space-y-5 animate-fade-in">

    {{-- Back + Aksi --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 text-sm transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Daftar Produk
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.products.edit', $product) }}"
                class="inline-flex items-center gap-2 text-sm bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 px-4 py-2 rounded-xl transition-colors">
                <i class="fa-solid fa-pen text-xs"></i> Edit
            </a>
            <a href="{{ route('admin.stock.masuk') }}?product_id={{ $product->id }}"
                class="inline-flex items-center gap-2 text-sm bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl transition-colors">
                <i class="fa-solid fa-arrow-down-to-bracket text-xs"></i> Stok Masuk
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ===== KOLOM KIRI: Foto + Barcode ===== --}}
        <div class="space-y-5">

            {{-- Foto Produk --}}
            <div class="bg-white rounded-2xl shadow-sm border border-surface-200 overflow-hidden">
                <div class="aspect-square bg-slate-50 flex items-center justify-center">
                    @if($product->foto)
                        <img src="{{ Storage::url($product->foto) }}" class="w-full h-full object-contain" alt="{{ $product->nama_produk }}">
                    @else
                        <div class="text-center p-8">
                            <i class="fa-solid fa-box text-slate-200 text-6xl mb-3"></i>
                            <p class="text-xs text-slate-400">Belum ada foto</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- === FASE 3.2: Barcode Display + Print === --}}
            <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-barcode text-violet-500"></i> Barcode
                    </h3>
                    <button onclick="printBarcode()"
                        class="text-xs bg-slate-700 text-white px-3 py-1.5 rounded-lg hover:bg-slate-800 transition-colors flex items-center gap-1">
                        <i class="fa-solid fa-print text-xs"></i> Print
                    </button>
                </div>

                {{-- Area untuk print --}}
                <div id="print-area" class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-center">
                    <p class="text-xs font-bold text-slate-800 mb-1">{{ config('app.name') }}</p>
                    <p class="text-xs text-slate-600 mb-2 leading-tight">{{ $product->nama_produk }}</p>
                    <svg id="barcode-main"></svg>
                    <p class="text-xs font-bold text-slate-700 mt-1">
                        Rp {{ number_format($product->harga_jual, 0, ',', '.') }}
                    </p>
                </div>

                {{-- Tombol copy SKU --}}
                <button type="button" onclick="copySku()"
                    class="mt-3 w-full text-xs text-slate-600 border border-slate-200 rounded-xl py-2 hover:bg-slate-50 transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-copy text-slate-400"></i>
                    Salin SKU: <span class="font-mono font-bold">{{ $product->sku }}</span>
                </button>
            </div>
        </div>

        {{-- ===== KOLOM KANAN: Info & Riwayat ===== --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Info Produk --}}
            <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-5">
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Kategori</p>
                        <p class="font-semibold text-slate-800 text-sm">
                            @if($product->category)
                            <i class="{{ $product->category->icon ?? 'fa-solid fa-tag' }} text-blue-500 mr-1"></i>
                            {{ $product->category->nama_kategori }}
                            @else — @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Satuan</p>
                        <p class="font-semibold text-slate-800 text-sm">{{ $product->satuan }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Status</p>
                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full
                            {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            <i class="fa-solid fa-circle text-xs"></i>
                            {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    {{-- === FASE 3.4: Tampilkan HPP yang dihitung otomatis === --}}
                    <div class="col-span-full pt-3 border-t border-slate-100 grid grid-cols-3 gap-5">
                        <div>
                            <p class="text-xs text-slate-400 mb-0.5">Modal / HPP</p>
                            <p class="font-bold text-slate-800">Rp {{ number_format($product->modal_hpp, 0, ',', '.') }}</p>
                            <p class="text-xs text-slate-400">Rata-rata tertimbang</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 mb-0.5">Harga Jual</p>
                            <p class="font-bold text-blue-700 text-lg">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 mb-0.5">Laba / unit</p>
                            @php $laba = $product->harga_jual - $product->modal_hpp; @endphp
                            <p class="font-bold {{ $laba >= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                                Rp {{ number_format($laba, 0, ',', '.') }}
                            </p>
                            @if($product->modal_hpp > 0)
                            <p class="text-xs text-emerald-500">{{ number_format(($laba / $product->modal_hpp) * 100, 1) }}%</p>
                            @endif
                        </div>
                    </div>

                    {{-- Stok --}}
                    <div class="col-span-full pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Stok</p>
                            <div class="flex gap-2">
                                <a href="{{ route('admin.stock.masuk') }}?product_id={{ $product->id }}"
                                    class="text-xs bg-emerald-600 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition-colors flex items-center gap-1">
                                    <i class="fa-solid fa-arrow-down-to-bracket text-xs"></i> Stok Masuk
                                </a>
                                <a href="{{ route('admin.stock.keluar') }}?product_id={{ $product->id }}"
                                    class="text-xs bg-rose-600 text-white px-3 py-1.5 rounded-lg hover:bg-rose-700 transition-colors flex items-center gap-1">
                                    <i class="fa-solid fa-arrow-up-from-bracket text-xs"></i> Keluar
                                </a>
                            </div>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4 flex items-center gap-4">
                            <div class="text-center">
                                <p class="text-3xl font-bold {{ $product->stok_saat_ini <= $product->stok_minimum ? 'text-amber-600' : 'text-slate-800' }}">
                                    {{ $product->stok_saat_ini }}
                                </p>
                                <p class="text-xs text-slate-400">{{ $product->satuan }}</p>
                            </div>
                            <div class="flex-1">
                                {{-- Progress bar stok --}}
                                @php $pct = $product->stok_minimum > 0 ? min(100, ($product->stok_saat_ini / ($product->stok_minimum * 3)) * 100) : 100; @endphp
                                <div class="w-full bg-slate-200 rounded-full h-2.5 mb-1">
                                    <div class="h-2.5 rounded-full transition-all {{ $product->stok_saat_ini <= $product->stok_minimum ? 'bg-amber-400' : 'bg-emerald-500' }}"
                                        style="width: {{ $pct }}%"></div>
                                </div>
                                <p class="text-xs text-slate-400">Minimum: {{ $product->stok_minimum }} {{ $product->satuan }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($product->deskripsi)
                <div class="pt-3 border-t border-slate-100 mt-4">
                    <p class="text-xs text-slate-400 mb-1">Deskripsi</p>
                    <p class="text-sm text-slate-700">{{ $product->deskripsi }}</p>
                </div>
                @endif
            </div>

            {{-- Riwayat Mutasi Stok Terakhir --}}
            <div class="bg-white rounded-2xl shadow-sm border border-surface-200">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-blue-500"></i> Riwayat Mutasi Stok
                    </h3>
                    <a href="{{ route('admin.stock.per-produk', $product) }}" class="text-xs text-blue-600 hover:underline">Lihat semua</a>
                </div>

                <div class="divide-y divide-slate-50">
                    @forelse($mutasiTerbaru as $m)
                    <div class="flex items-center gap-3 px-6 py-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                            {{ $m->tipe === 'masuk' ? 'bg-emerald-100' : 'bg-rose-100' }}">
                            <i class="fa-solid {{ $m->tipe === 'masuk' ? 'fa-arrow-down text-emerald-600' : 'fa-arrow-up text-rose-600' }} text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 truncate">
                                {{ $m->tipe === 'masuk' ? '+' : '-' }}{{ $m->jumlah }} {{ $product->satuan }}
                                <span class="text-slate-400 font-normal">→ {{ $m->stok_sesudah }} {{ $product->satuan }}</span>
                            </p>
                            <p class="text-xs text-slate-400 truncate">{{ $m->keterangan }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            @if($m->tipe === 'masuk' && $m->harga_beli)
                            <p class="text-xs text-violet-600 font-mono">Rp {{ number_format($m->harga_beli, 0, ',', '.') }}/{{ $product->satuan }}</p>
                            @endif
                            <p class="text-xs text-slate-400">{{ $m->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center">
                        <i class="fa-solid fa-inbox text-slate-200 text-3xl mb-2"></i>
                        <p class="text-slate-400 text-sm">Belum ada riwayat mutasi</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.6/JsBarcode.all.min.js"></script>
<script>
// Generate barcode saat halaman load
window.addEventListener('DOMContentLoaded', () => {
    JsBarcode('#barcode-main', '{{ $product->sku }}', {
        format: 'CODE128',
        width: 2, height: 60,
        displayValue: true,
        fontSize: 13,
        margin: 8,
        fontOptions: 'bold'
    });
});

// Print barcode label
function printBarcode() {
    window.print();
}

// Salin SKU ke clipboard
function copySku() {
    navigator.clipboard.writeText('{{ $product->sku }}').then(() => {
        Toast.fire({ icon: 'success', title: 'SKU berhasil disalin!' });
    });
}
</script>
@endpush
