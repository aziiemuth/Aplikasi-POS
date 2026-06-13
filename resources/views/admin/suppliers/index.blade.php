@extends('layouts.app')
@section('title', 'Data Supplier')
@section('page-title', 'Data Supplier')
@section('page-subtitle', 'Kelola daftar pemasok barang — hanya terlihat oleh Admin')

@section('content')
<div class="space-y-5 animate-fade-in">

    <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
        <form action="{{ route('admin.suppliers.index') }}" method="GET" class="flex items-center gap-2">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kontak, email..."
                    class="pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all w-64">
            </div>
            <button type="submit" class="bg-slate-700 text-white text-sm px-4 py-2.5 rounded-xl hover:bg-slate-800 transition-colors">Cari</button>
            @if(request('search'))
            <a href="{{ route('admin.suppliers.index') }}" class="text-slate-500 text-sm px-3 py-2.5 rounded-xl hover:bg-slate-100 transition-colors">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.suppliers.create') }}"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm hover:-translate-y-0.5 transition-all">
            <i class="fa-solid fa-truck-ramp-box"></i> Tambah Supplier
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-surface-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nama Supplier</th>
                    <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Kontak</th>
                    <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Alamat</th>
                    <th class="text-center px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Transaksi</th>
                    <th class="text-center px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-center px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($suppliers as $sup)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-violet-100 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-truck text-violet-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $sup->nama_supplier }}</p>
                                @if($sup->email)
                                <p class="text-xs text-slate-400">{{ $sup->email }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        @if($sup->kontak)
                        <a href="tel:{{ $sup->kontak }}" class="text-blue-600 hover:underline text-sm flex items-center gap-1">
                            <i class="fa-solid fa-phone text-xs"></i> {{ $sup->kontak }}
                        </a>
                        @else
                        <span class="text-slate-300 text-sm">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-4 text-xs text-slate-500 max-w-xs">
                        <p class="line-clamp-2">{{ $sup->alamat ?? '—' }}</p>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="text-sm font-semibold text-slate-700">{{ number_format($sup->stock_mutations_count) }}</span>
                        <p class="text-xs text-slate-400">mutasi</p>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full
                            {{ $sup->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            <i class="fa-solid fa-circle text-xs"></i>
                            {{ $sup->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('admin.suppliers.edit', $sup) }}" title="Edit"
                                class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors">
                                <i class="fa-solid fa-pen text-sm"></i>
                            </a>
                            <form action="{{ route('admin.suppliers.toggle', $sup) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" title="{{ $sup->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                    class="p-2 {{ $sup->is_active ? 'text-amber-600 hover:bg-amber-100' : 'text-emerald-600 hover:bg-emerald-100' }} rounded-lg transition-colors">
                                    <i class="fa-solid {{ $sup->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} text-sm"></i>
                                </button>
                            </form>
                            @if($sup->stock_mutations_count === 0)
                            <form action="{{ route('admin.suppliers.destroy', $sup) }}" method="POST" id="del-sup-{{ $sup->id }}">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete('del-sup-{{ $sup->id }}', '{{ $sup->nama_supplier }}')"
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
                    <td colspan="6" class="px-6 py-16 text-center">
                        <i class="fa-solid fa-truck text-slate-200 text-4xl mb-3"></i>
                        <p class="text-slate-400 font-medium">Belum ada data supplier</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($suppliers->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">{{ $suppliers->links() }}</div>
        @endif
    </div>
</div>
@endsection
