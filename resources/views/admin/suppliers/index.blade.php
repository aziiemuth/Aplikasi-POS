@extends('layouts.app')
@section('title', 'Data Supplier')
@section('page-title', 'Data Supplier')
@section('page-subtitle', 'Kelola daftar pemasok barang — hanya terlihat oleh Admin')

@section('content')
<div class="space-y-5 animate-fade-in">

    {{-- ===== HEADER BAR ===== --}}
    <div class="flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between">
        {{-- Search Input (Realtime) --}}
        <div class="relative w-full sm:max-w-xs flex-1">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
            </div>
            <input type="text" id="search-input" value="{{ request('search') }}" placeholder="Cari nama, kontak, email..."
                class="pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all w-full shadow-sm">
        </div>

        {{-- Tambah Supplier --}}
        <a href="{{ route('admin.suppliers.create') }}"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm hover:-translate-y-0.5 transition-all">
            <i class="fa-solid fa-truck-ramp-box"></i> Tambah Supplier
        </a>
    </div>

    {{-- Tabel Supplier --}}
    <div id="suppliers-container" class="bg-white rounded-2xl shadow-sm border border-surface-200 overflow-hidden relative">
        @include('admin.suppliers._table')
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let searchTimer = null;
        const searchInput = document.getElementById('search-input');
        const container = document.getElementById('suppliers-container');

        function fetchSuppliers(query = '', pageUrl = null) {
            let url = pageUrl || "{{ route('admin.suppliers.index') }}?search=" + encodeURIComponent(query);
            
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
                console.error('Error fetching suppliers:', error);
                container.style.opacity = '1';
            });
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                fetchSuppliers(this.value);
            }, 300);
        });

        // Intercept pagination clicks
        container.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href && (link.href.includes('page=') || link.getAttribute('rel') === 'prev' || link.getAttribute('rel') === 'next')) {
                e.preventDefault();
                fetchSuppliers(searchInput.value, link.href);
                container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    });
</script>
@endpush
</div>
@endsection
