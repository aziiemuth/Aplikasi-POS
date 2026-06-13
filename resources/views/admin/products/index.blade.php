@extends('layouts.app')
@section('title', 'Daftar Produk')
@section('page-title', 'Daftar Produk')
@section('page-subtitle', 'Master data produk — kelola SKU, harga, stok, dan gambar')

@section('content')
<div class="space-y-5 animate-fade-in">    {{-- Filter Bar --}}
    <div class="flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between">
        <div class="flex flex-wrap items-center gap-3 flex-1">
            {{-- Search input --}}
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                </div>
                <input type="text" id="search-input" value="{{ request('search') }}" placeholder="Cari nama atau SKU..."
                    class="pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-100 outline-none w-full shadow-sm">
            </div>

            {{-- Category select --}}
            <select id="category-select" class="text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:ring-2 focus:ring-blue-100 outline-none flex-1 sm:flex-none shadow-sm">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->nama_kategori }}</option>
                @endforeach
            </select>

            {{-- Stock select --}}
            <select id="stok-select" class="text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:ring-2 focus:ring-blue-100 outline-none flex-1 sm:flex-none shadow-sm">
                <option value="">Semua Stok</option>
                <option value="tipis" {{ request('stok') == 'tipis' ? 'selected' : '' }}>⚠️ Stok Tipis</option>
                <option value="kosong" {{ request('stok') == 'kosong' ? 'selected' : '' }}>❌ Stok Kosong</option>
            </select>
        </div>

        {{-- Tambah Produk --}}
        <a href="{{ route('admin.products.create') }}"
            class="w-full lg:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm hover:-translate-y-0.5 transition-all whitespace-nowrap">
            <i class="fa-solid fa-plus"></i> Tambah Produk
        </a>
    </div>

    {{-- Tabel Produk --}}
    <div id="products-container" class="bg-white rounded-2xl shadow-sm border border-surface-200 overflow-hidden relative">
        @include('admin.products._table')
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let searchTimer = null;
        const searchInput = document.getElementById('search-input');
        const categorySelect = document.getElementById('category-select');
        const stokSelect = document.getElementById('stok-select');
        const container = document.getElementById('products-container');

        function fetchProducts(pageUrl = null) {
            const query = searchInput.value;
            const category = categorySelect.value;
            const stok = stokSelect.value;

            let url = pageUrl || "{{ route('admin.products.index') }}";
            
            const urlObj = new URL(url, window.location.origin);
            if (query) urlObj.searchParams.set('search', query);
            if (category) urlObj.searchParams.set('category', category);
            if (stok) urlObj.searchParams.set('stok', stok);
            
            container.style.opacity = '0.5';
            container.style.transition = 'opacity 0.15s ease';

            fetch(urlObj.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
                container.style.opacity = '1';
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                container.style.opacity = '1';
            });
        }

        // Search text input keyup
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                fetchProducts();
            }, 300);
        });

        // Dropdowns change
        categorySelect.addEventListener('change', function() {
            fetchProducts();
        });

        stokSelect.addEventListener('change', function() {
            fetchProducts();
        });

        // Intercept pagination clicks
        container.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href && (link.href.includes('page=') || link.getAttribute('rel') === 'prev' || link.getAttribute('rel') === 'next')) {
                e.preventDefault();
                fetchProducts(link.href);
                container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    });
</script>
@endpush
    </div>
</div>
@endsection
