@extends('layouts.app')
@section('title', 'Kategori Produk')
@section('page-title', 'Kategori Produk')
@section('page-subtitle', 'Kelola kategori untuk pengelompokan produk di POS')

@section('content')
<div class="space-y-5 animate-fade-in">

    {{-- ===== HEADER BAR ===== --}}
    <div class="flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between">
        {{-- Search Input (Realtime) --}}
        <div class="relative w-full sm:max-w-xs flex-1">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
            </div>
            <input type="text" id="search-input" value="{{ request('search') }}" placeholder="Cari kategori..."
                class="pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all w-full shadow-sm">
        </div>

        {{-- Tambah Kategori --}}
        <a href="{{ route('admin.categories.create') }}"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm shadow-blue-500/30 hover:shadow-blue-500/40 transition-all hover:-translate-y-0.5">
            <i class="fa-solid fa-plus"></i> Tambah Kategori
        </a>
    </div>

    {{-- Grid Kategori --}}
    <div id="categories-container" class="relative">
        @include('admin.categories._grid')
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let searchTimer = null;
        const searchInput = document.getElementById('search-input');
        const container = document.getElementById('categories-container');

        function fetchCategories(query = '', pageUrl = null) {
            let url = pageUrl || "{{ route('admin.categories.index') }}?search=" + encodeURIComponent(query);
            
            if (pageUrl && query) {
                const urlObj = new URL(pageUrl);
                urlObj.searchParams.set('search', query);
                url = urlObj.toString();
            }

            container.style.opacity = '0.5';
            container.style.transition = 'opacity 0.15s ease';

            fetch(url, {
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
                console.error('Error fetching categories:', error);
                container.style.opacity = '1';
            });
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                fetchCategories(this.value);
            }, 300);
        });

        // Intercept pagination clicks
        container.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href && (link.href.includes('page=') || link.getAttribute('rel') === 'prev' || link.getAttribute('rel') === 'next')) {
                e.preventDefault();
                fetchCategories(searchInput.value, link.href);
                container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    });
</script>
@endpush
</div>
@endsection
