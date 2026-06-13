@extends('layouts.app')
@section('title', 'Riwayat Mutasi Stok')
@section('page-title', 'Riwayat Mutasi Stok')
@section('page-subtitle', 'Kartu stok lengkap semua produk — hanya Admin yang bisa melihat')

@section('content')
<div class="space-y-5 animate-fade-in">

    {{-- Filter Bar --}}
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <form action="{{ route('admin.stock.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <select name="product_id" class="text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:ring-2 focus:ring-blue-100 outline-none">
                <option value="">Semua Produk</option>
                @foreach($products as $p)
                <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_produk }}</option>
                @endforeach
            </select>
            <select name="tipe" class="text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:ring-2 focus:ring-blue-100 outline-none">
                <option value="">Semua Tipe</option>
                <option value="masuk"  {{ request('tipe') == 'masuk' ? 'selected' : '' }}>📥 Stok Masuk</option>
                <option value="keluar" {{ request('tipe') == 'keluar' ? 'selected' : '' }}>📤 Stok Keluar</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                class="text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white outline-none">
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                class="text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white outline-none">
            <button type="submit" class="bg-slate-700 text-white text-sm px-4 py-2.5 rounded-xl hover:bg-slate-800 transition-colors">Filter</button>
            @if(request()->hasAny(['product_id','tipe','date_from','date_to']))
            <a href="{{ route('admin.stock.index') }}" class="text-slate-500 text-sm px-3 py-2.5 rounded-xl hover:bg-slate-100 transition-colors">Reset</a>
            @endif
        </form>

        <div class="flex gap-2">
            <a href="{{ route('admin.stock.masuk') }}"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all">
                <i class="fa-solid fa-arrow-down-to-bracket"></i> Stok Masuk
            </a>
            <a href="{{ route('admin.stock.keluar') }}"
                class="inline-flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-all">
                <i class="fa-solid fa-arrow-up-from-bracket"></i> Stok Keluar
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-surface-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left px-5 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                    <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe</th>
                    <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Produk</th>
                    <th class="text-center px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah</th>
                    <th class="text-center px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok Sebelum → Sesudah</th>
                    <th class="text-right px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Harga Beli</th>
                    <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Keterangan</th>
                    <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Admin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($mutations as $m)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-3">
                        <p class="text-xs font-medium text-slate-700">{{ $m->created_at->format('d M Y') }}</p>
                        <p class="text-xs text-slate-400">{{ $m->created_at->format('H:i') }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full
                            {{ $m->tipe === 'masuk' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            <i class="fa-solid {{ $m->tipe === 'masuk' ? 'fa-arrow-down' : 'fa-arrow-up' }} text-xs"></i>
                            {{ $m->tipe === 'masuk' ? 'Masuk' : 'Keluar' }}
                        </span>
                        @if($m->order_id)
                        <p class="text-xs text-blue-500 mt-0.5">via POS</p>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.products.show', $m->product_id) }}" class="font-semibold text-slate-700 hover:text-blue-600 text-xs">
                            {{ $m->product?->nama_produk ?? 'N/A' }}
                        </a>
                        @if($m->supplier)
                        <p class="text-xs text-violet-500 mt-0.5">
                            <i class="fa-solid fa-truck text-xs"></i> {{ $m->supplier->nama_supplier }}
                        </p>
                        @endif
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
                    <td class="px-4 py-3 text-xs text-slate-500 max-w-xs">
                        <p class="line-clamp-2">{{ $m->keterangan }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-500">
                        {{ $m->user?->name ?? 'Sistem' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center">
                        <i class="fa-solid fa-clipboard-list text-slate-200 text-4xl mb-3"></i>
                        <p class="text-slate-400 font-medium">Belum ada riwayat mutasi stok</p>
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
