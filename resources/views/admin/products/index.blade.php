@extends('layouts.app')
@section('title', 'Daftar Produk')
@section('page-title', 'Daftar Produk')
@section('page-subtitle', 'Master data produk — kelola SKU, harga, stok, dan gambar')

@section('content')
<div class="space-y-5 animate-fade-in">

    {{-- Filter Bar --}}
    <div class="flex flex-col lg:flex-row gap-3 lg:items-center lg:justify-between">
        <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau SKU..."
                    class="pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-100 outline-none w-56">
            </div>
            <select name="category" class="text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:ring-2 focus:ring-blue-100 outline-none">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                @endforeach
            </select>
            <select name="stok" class="text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:ring-2 focus:ring-blue-100 outline-none">
                <option value="">Semua Stok</option>
                <option value="tipis" {{ request('stok') == 'tipis' ? 'selected' : '' }}>⚠️ Stok Tipis</option>
                <option value="kosong" {{ request('stok') == 'kosong' ? 'selected' : '' }}>❌ Stok Kosong</option>
            </select>
            <button type="submit" class="bg-slate-700 text-white text-sm px-4 py-2.5 rounded-xl hover:bg-slate-800 transition-colors">Filter</button>
            @if(request()->hasAny(['search','category','stok']))
            <a href="{{ route('admin.products.index') }}" class="text-slate-500 text-sm px-3 py-2.5 rounded-xl hover:bg-slate-100 transition-colors flex items-center gap-1">
                <i class="fa-solid fa-xmark"></i> Reset
            </a>
            @endif
        </form>

        <a href="{{ route('admin.products.create') }}"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm hover:-translate-y-0.5 transition-all whitespace-nowrap">
            <i class="fa-solid fa-plus"></i> Tambah Produk
        </a>
    </div>

    {{-- Tabel Produk --}}
    <div class="bg-white rounded-2xl shadow-sm border border-surface-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Produk</th>
                    <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">SKU/Barcode</th>
                    <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
                    <th class="text-right px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">HPP</th>
                    <th class="text-right px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Harga Jual</th>
                    <th class="text-center px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok</th>
                    <th class="text-center px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($products as $product)
                @php
                    $isLow = !$product->trashed() && $product->stok_saat_ini <= $product->stok_minimum;
                    $isEmpty = !$product->trashed() && $product->stok_saat_ini === 0;
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors {{ $product->trashed() ? 'opacity-50 bg-red-50/20' : '' }}">
                    {{-- Foto + Nama --}}
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                @if($product->foto)
                                    <img src="{{ Storage::url($product->foto) }}" class="w-full h-full object-contain" alt="{{ $product->nama_produk }}">
                                @else
                                    <i class="fa-solid fa-box text-slate-300 text-xl"></i>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-800 truncate max-w-[180px]">{{ $product->nama_produk }}</p>
                                <p class="text-xs text-slate-400">{{ $product->satuan }}</p>
                            </div>
                        </div>
                    </td>
                    {{-- SKU --}}
                    <td class="px-4 py-3">
                        <span class="font-mono text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">{{ $product->sku }}</span>
                    </td>
                    {{-- Kategori --}}
                    <td class="px-4 py-3">
                        @if($product->category)
                        <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full bg-blue-50 text-blue-700">
                            <i class="{{ $product->category->icon ?? 'fa-solid fa-tag' }} text-xs"></i>
                            {{ $product->category->nama_kategori }}
                        </span>
                        @else
                        <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>
                    {{-- HPP --}}
                    <td class="px-4 py-3 text-right">
                        <p class="text-sm text-slate-600">Rp {{ number_format($product->modal_hpp, 0, ',', '.') }}</p>
                    </td>
                    {{-- Harga Jual --}}
                    <td class="px-4 py-3 text-right">
                        <p class="text-sm font-semibold text-slate-800">Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</p>
                        @if($product->modal_hpp > 0)
                        <p class="text-xs text-emerald-600">+Rp {{ number_format($product->harga_jual - $product->modal_hpp, 0, ',', '.') }}</p>
                        @endif
                    </td>
                    {{-- Stok --}}
                    <td class="px-4 py-3 text-center">
                        @if($product->trashed())
                            <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full font-semibold">Dihapus</span>
                        @elseif($isEmpty)
                            <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-bold">KOSONG</span>
                        @elseif($isLow)
                            <div>
                                <span class="text-sm font-bold text-amber-600">{{ $product->stok_saat_ini }}</span>
                                <span class="text-xs text-amber-500 block">⚠️ Tipis!</span>
                            </div>
                        @else
                            <span class="text-sm font-bold text-slate-700">{{ $product->stok_saat_ini }}</span>
                            <span class="text-xs text-slate-400 block">{{ $product->satuan }}</span>
                        @endif
                    </td>
                    {{-- Aksi --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1">
                            @if($product->trashed())
                                {{-- Tombol Restore untuk produk yang dihapus --}}
                                <form action="{{ route('admin.products.restore', $product->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="Pulihkan produk ini"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-100 hover:bg-emerald-200 rounded-lg transition-colors">
                                        <i class="fa-solid fa-rotate-left text-xs"></i> Pulihkan
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('admin.products.show', $product) }}" title="Detail & Barcode"
                                    class="p-2 text-violet-600 hover:bg-violet-100 rounded-lg transition-colors">
                                    <i class="fa-solid fa-barcode text-sm"></i>
                                </a>
                                <a href="{{ route('admin.stock.per-produk', $product) }}" title="Riwayat Stok"
                                    class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors">
                                    <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}" title="Edit"
                                    class="p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" id="del-prod-{{ $product->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete('del-prod-{{ $product->id }}', '{{ $product->nama_produk }}')"
                                        class="p-2 text-red-500 hover:bg-red-100 rounded-lg transition-colors">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <i class="fa-solid fa-boxes-stacked text-slate-200 text-4xl mb-3"></i>
                        <p class="text-slate-400 font-medium">Belum ada produk</p>
                        <a href="{{ route('admin.products.create') }}" class="mt-3 inline-flex items-center gap-2 text-blue-600 text-sm hover:underline">
                            <i class="fa-solid fa-plus text-xs"></i> Tambah produk pertama
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($products->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $products->links() }}</div>
        @endif
    </div>
</div>
@endsection
