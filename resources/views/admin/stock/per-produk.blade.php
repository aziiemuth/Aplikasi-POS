@extends('layouts.app')
@section('title', 'Kartu Stok: ' . $product->nama_produk)
@section('page-title', 'Kartu Stok Produk')
@section('page-subtitle', $product->nama_produk . ' — SKU: ' . $product->sku)

@section('content')
<div class="space-y-5 animate-fade-in">

    {{-- Header Produk --}}
    <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-5 flex items-center gap-4">
        <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center overflow-hidden shrink-0">
            @if($product->foto)
                <img src="{{ Storage::url($product->foto) }}" class="w-full h-full object-contain">
            @else
                <i class="fa-solid fa-box text-slate-300 text-2xl"></i>
            @endif
        </div>
        <div class="flex-1">
            <h2 class="font-bold text-slate-800">{{ $product->nama_produk }}</h2>
            <p class="text-sm text-slate-400">SKU: <span class="font-mono">{{ $product->sku }}</span> · {{ $product->category?->nama_kategori ?? 'Tanpa Kategori' }}</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-slate-400">Stok Saat Ini</p>
            <p class="text-3xl font-bold {{ $product->stok_saat_ini <= $product->stok_minimum ? 'text-amber-600' : 'text-slate-800' }}">
                {{ $product->stok_saat_ini }}
            </p>
            <p class="text-xs text-slate-400">{{ $product->satuan }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.stock.masuk') }}?product_id={{ $product->id }}"
                class="inline-flex items-center gap-1.5 bg-emerald-600 text-white text-xs px-3 py-2 rounded-xl hover:bg-emerald-700 transition-colors">
                <i class="fa-solid fa-arrow-down-to-bracket text-xs"></i> Stok Masuk
            </a>
            <a href="{{ route('admin.products.show', $product) }}"
                class="inline-flex items-center gap-1.5 bg-white border border-slate-200 text-slate-600 text-xs px-3 py-2 rounded-xl hover:bg-slate-50 transition-colors">
                <i class="fa-solid fa-barcode text-xs"></i> Detail & Barcode
            </a>
        </div>
    </div>

    {{-- Tabel Mutasi --}}
    <div class="bg-white rounded-2xl shadow-sm border border-surface-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-blue-500"></i> Riwayat Lengkap Mutasi Stok
            </h3>
            <span class="text-xs text-slate-400 bg-slate-100 px-2 py-1 rounded-full">{{ $mutations->total() }} entri</span>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Sebelum → Sesudah</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Harga Beli</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Supplier</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($mutations as $m)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-3 text-xs">
                        <p class="font-medium text-slate-700">{{ $m->created_at->format('d M Y') }}</p>
                        <p class="text-slate-400">{{ $m->created_at->format('H:i') }}</p>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full
                            {{ $m->tipe === 'masuk' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            <i class="fa-solid {{ $m->tipe === 'masuk' ? 'fa-arrow-down' : 'fa-arrow-up' }} text-xs"></i>
                            {{ ucfirst($m->tipe) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-bold {{ $m->tipe === 'masuk' ? 'text-emerald-700' : 'text-rose-600' }}">
                            {{ $m->tipe === 'masuk' ? '+' : '-' }}{{ $m->jumlah }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center font-mono text-xs text-slate-600">
                        {{ $m->stok_sebelum }} → <strong>{{ $m->stok_sesudah }}</strong>
                    </td>
                    <td class="px-4 py-3 text-right text-xs">
                        @if($m->harga_beli)
                        <span class="text-violet-700 font-mono">Rp {{ number_format($m->harga_beli, 0, ',', '.') }}</span>
                        @else
                        <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-500">{{ $m->supplier?->nama_supplier ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-slate-500">{{ $m->keterangan }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <i class="fa-solid fa-inbox text-slate-200 text-3xl mb-2"></i>
                        <p class="text-slate-400 text-sm">Belum ada riwayat mutasi</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($mutations->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $mutations->links() }}</div>
        @endif
    </div>
</div>
@endsection
