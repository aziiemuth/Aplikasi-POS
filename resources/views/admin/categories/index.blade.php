@extends('layouts.app')
@section('title', 'Kategori Produk')
@section('page-title', 'Kategori Produk')
@section('page-subtitle', 'Kelola kategori untuk pengelompokan produk di POS')

@section('content')
<div class="space-y-5 animate-fade-in">

    <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
        <form action="{{ route('admin.categories.index') }}" method="GET" class="flex items-center gap-2">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kategori..."
                    class="pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all w-56">
            </div>
            <button type="submit" class="bg-slate-700 text-white text-sm px-4 py-2.5 rounded-xl hover:bg-slate-800 transition-colors">Cari</button>
            @if(request('search'))
            <a href="{{ route('admin.categories.index') }}" class="text-slate-500 text-sm px-3 py-2.5 rounded-xl hover:bg-slate-100 transition-colors">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.categories.create') }}"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm hover:-translate-y-0.5 transition-all">
            <i class="fa-solid fa-plus"></i> Tambah Kategori
        </a>
    </div>

    {{-- Grid Kategori --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($categories as $cat)
        <div class="bg-white rounded-2xl shadow-sm border {{ $cat->is_active ? 'border-surface-200' : 'border-slate-200 opacity-60' }} p-5 hover:shadow-md transition-all group">
            <div class="flex items-start justify-between mb-3">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0
                    {{ $cat->is_active ? 'bg-blue-100' : 'bg-slate-100' }}">
                    <i class="{{ $cat->icon ?? 'fa-solid fa-tag' }} {{ $cat->is_active ? 'text-blue-600' : 'text-slate-400' }} text-lg"></i>
                </div>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <a href="{{ route('admin.categories.edit', $cat) }}" title="Edit"
                        class="p-1.5 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </a>
                    <form action="{{ route('admin.categories.toggle', $cat) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" title="{{ $cat->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                            class="p-1.5 {{ $cat->is_active ? 'text-amber-500 hover:bg-amber-100' : 'text-emerald-600 hover:bg-emerald-100' }} rounded-lg transition-colors">
                            <i class="fa-solid {{ $cat->is_active ? 'fa-eye-slash' : 'fa-eye' }} text-xs"></i>
                        </button>
                    </form>
                    @if($cat->products_count === 0)
                    <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" id="del-cat-{{ $cat->id }}">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmDelete('del-cat-{{ $cat->id }}', '{{ $cat->nama_kategori }}')"
                            class="p-1.5 text-red-500 hover:bg-red-100 rounded-lg transition-colors">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <h3 class="font-bold text-slate-800 text-sm mb-1">{{ $cat->nama_kategori }}</h3>
            <p class="text-xs text-slate-400 line-clamp-2 mb-3">{{ $cat->deskripsi ?? 'Tidak ada deskripsi' }}</p>

            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-600">
                    <i class="fa-solid fa-boxes-stacked text-slate-400 mr-1"></i>
                    {{ $cat->products_count }} produk
                </span>
                <span class="text-xs px-2 py-0.5 rounded-full font-semibold
                    {{ $cat->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl border border-surface-200 px-6 py-16 text-center">
            <i class="fa-solid fa-tags text-slate-200 text-4xl mb-3"></i>
            <p class="text-slate-400 font-medium">Belum ada kategori</p>
            <a href="{{ route('admin.categories.create') }}" class="mt-3 inline-flex items-center gap-2 text-blue-600 text-sm hover:underline">
                <i class="fa-solid fa-plus text-xs"></i> Tambah kategori pertama
            </a>
        </div>
        @endforelse
    </div>

    @if($categories->hasPages())
    <div>{{ $categories->links() }}</div>
    @endif
</div>
@endsection
