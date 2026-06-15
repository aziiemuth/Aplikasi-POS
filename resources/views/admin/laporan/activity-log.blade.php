@extends('layouts.app')

@section('title', 'Log Aktivitas User')
@section('page-title', 'Log Aktivitas')
@section('page-subtitle', 'Pantau seluruh pergerakan dan aktivitas pengguna sistem')

@section('content')
<div class="space-y-5 animate-fade-in">

    {{-- ===== FILTER ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-5">
        <form id="filter-form" method="GET" action="{{ route('admin.laporan.activity-log') }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Filter User</label>
                    <select name="user_id" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-violet-100 outline-none">
                        <option value="">-- Semua User --</option>
                        @foreach($userList as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ ucfirst($user->role) }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Filter Aksi</label>
                    <input type="text" name="aksi" value="{{ request('aksi') }}" placeholder="cth: Login, Checkout..."
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-violet-100 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Filter Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                        class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-violet-100 outline-none">
                </div>
                <div class="flex gap-2 items-end">
                    <button type="submit"
                        class="hidden flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-sm">
                        <i class="fa-solid fa-magnifying-glass"></i> <span>Filter</span>
                    </button>
                    <a href="{{ route('admin.laporan.activity-log') }}"
                        class="flex items-center justify-center w-11 h-11 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition-colors shrink-0" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div id="result-container">
    {{-- ===== TABEL LOG ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-4 sm:px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 min-w-0">
                <i class="fa-solid fa-shield-halved text-violet-500 shrink-0"></i>
                <h3 class="font-bold text-slate-800 text-sm truncate">Audit Trail — Rekam Jejak Aktivitas</h3>
            </div>
            <span class="text-xs text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full font-medium shrink-0">{{ $logs->total() }} data</span>
        </div>

        {{-- Mobile: Card view --}}
        <div class="sm:hidden divide-y divide-slate-100">
            @forelse($logs as $log)
            @php
                $aksiLower = strtolower($log->aksi);
                $badgeConfig = match(true) {
                    str_contains($aksiLower, 'login')     => ['bg-blue-50',    'text-blue-700',    'fa-right-to-bracket'],
                    str_contains($aksiLower, 'logout')    => ['bg-slate-100',  'text-slate-600',   'fa-right-from-bracket'],
                    str_contains($aksiLower, 'checkout')  => ['bg-emerald-50', 'text-emerald-700', 'fa-check-circle'],
                    str_contains($aksiLower, 'hold') || str_contains($aksiLower, 'tahan') => ['bg-amber-50',   'text-amber-700',   'fa-pause'],
                    str_contains($aksiLower, 'stok')      => ['bg-violet-50',  'text-violet-700',  'fa-boxes-stacked'],
                    str_contains($aksiLower, 'hapus') || str_contains($aksiLower, 'delete') || str_contains($aksiLower, 'batal') => ['bg-rose-50', 'text-rose-700', 'fa-trash'],
                    str_contains($aksiLower, 'tambah') || str_contains($aksiLower, 'create') => ['bg-teal-50', 'text-teal-700', 'fa-plus'],
                    str_contains($aksiLower, 'edit') || str_contains($aksiLower, 'update') || str_contains($aksiLower, 'ubah') || str_contains($aksiLower, 'lanjut') => ['bg-indigo-50', 'text-indigo-700', 'fa-pen'],
                    default => ['bg-slate-50', 'text-slate-600', 'fa-circle-dot'],
                };
            @endphp
            <div class="px-4 py-3 space-y-2">
                <div class="flex items-start justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-semibold {{ $badgeConfig[0] }} {{ $badgeConfig[1] }}">
                        <i class="fa-solid {{ $badgeConfig[2] }} text-[10px]"></i>
                        {{ $log->aksi }}
                    </span>
                    <span class="text-xs text-slate-400 shrink-0">{{ $log->created_at->diffForHumans() }}</span>
                </div>
                <div class="flex items-center gap-2">
                    @if($log->user)
                    <span class="font-semibold text-slate-800 text-xs">{{ $log->user->name }}</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded font-medium
                        {{ $log->user->role === 'admin' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ ucfirst($log->user->role) }}
                    </span>
                    @else
                    <span class="text-xs text-slate-400 italic">Sistem</span>
                    @endif
                </div>
                @if($log->deskripsi)
                <p class="text-xs text-slate-500">{{ $log->deskripsi }}</p>
                @endif
                <div class="flex items-center gap-2 text-xs text-slate-400">
                    <i class="fa-solid fa-clock text-[10px]"></i>
                    <span>{{ $log->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</span>
                    @if($log->ip_address)
                    <span class="ml-auto font-mono">{{ $log->ip_address }}</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-5 py-12 text-center text-slate-400">
                <i class="fa-solid fa-shield-halved text-slate-200 text-4xl mb-3 block"></i>
                Tidak ada data log yang cocok
            </div>
            @endforelse
        </div>

        {{-- Desktop: Table view --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap">Waktu</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                    @php
                        $aksiLower = strtolower($log->aksi);
                        $badgeConfig = match(true) {
                            str_contains($aksiLower, 'login')     => ['bg-blue-50',    'text-blue-700',    'fa-right-to-bracket'],
                            str_contains($aksiLower, 'logout')    => ['bg-slate-100',  'text-slate-600',   'fa-right-from-bracket'],
                            str_contains($aksiLower, 'checkout')  => ['bg-emerald-50', 'text-emerald-700', 'fa-check-circle'],
                            str_contains($aksiLower, 'hold') || str_contains($aksiLower, 'tahan') => ['bg-amber-50',   'text-amber-700',   'fa-pause'],
                            str_contains($aksiLower, 'stok')      => ['bg-violet-50',  'text-violet-700',  'fa-boxes-stacked'],
                            str_contains($aksiLower, 'hapus') || str_contains($aksiLower, 'delete') || str_contains($aksiLower, 'batal') => ['bg-rose-50', 'text-rose-700', 'fa-trash'],
                            str_contains($aksiLower, 'tambah') || str_contains($aksiLower, 'create') => ['bg-teal-50', 'text-teal-700', 'fa-plus'],
                            str_contains($aksiLower, 'edit') || str_contains($aksiLower, 'update') || str_contains($aksiLower, 'ubah') || str_contains($aksiLower, 'lanjut') => ['bg-indigo-50', 'text-indigo-700', 'fa-pen'],
                            default => ['bg-slate-50', 'text-slate-600', 'fa-circle-dot'],
                        };
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3 text-xs text-slate-500 whitespace-nowrap">
                            <p class="font-medium text-slate-700">{{ $log->created_at->timezone('Asia/Jakarta')->format('d/m/Y') }}</p>
                            <p class="text-slate-400">{{ $log->created_at->timezone('Asia/Jakarta')->format('H:i:s') }}</p>
                        </td>
                        <td class="px-5 py-3">
                            @if($log->user)
                            <p class="font-semibold text-slate-800 text-xs">{{ $log->user->name }}</p>
                            <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded font-medium
                                {{ $log->user->role === 'admin' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ ucfirst($log->user->role) }}
                            </span>
                            @else
                            <span class="text-xs text-slate-400 italic">Sistem</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-semibold {{ $badgeConfig[0] }} {{ $badgeConfig[1] }}">
                                <i class="fa-solid {{ $badgeConfig[2] }} text-[10px]"></i>
                                {{ $log->aksi }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-slate-600 max-w-[200px] truncate">{{ $log->deskripsi ?? '-' }}</td>
                        <td class="px-5 py-3 text-xs font-mono text-slate-500">{{ $log->ip_address ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                            <i class="fa-solid fa-shield-halved text-slate-200 text-4xl mb-3 block"></i>
                            Tidak ada data log yang cocok
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-4 sm:px-5 py-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let filterTimer;
        const form = document.getElementById('filter-form');
        const container = document.getElementById('result-container');

        if(form && container) {
            function fetchResults() {
                const url = new URL(form.action);
                const formData = new FormData(form);
                for (const [key, value] of formData.entries()) {
                    if (value) {
                        url.searchParams.set(key, value);
                    }
                }

                container.style.opacity = '0.5';
                
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContainer = doc.getElementById('result-container');
                    
                    if (newContainer) {
                        container.innerHTML = newContainer.innerHTML;
                    }
                    container.style.opacity = '1';
                    
                    window.history.pushState({}, '', url);
                });
            }

            form.querySelectorAll('input, select').forEach(el => {
                el.addEventListener('input', () => {
                    clearTimeout(filterTimer);
                    filterTimer = setTimeout(fetchResults, 300);
                });
                
                el.addEventListener('change', () => {
                    if(el.type !== 'text' && el.type !== 'search') {
                        clearTimeout(filterTimer);
                        fetchResults();
                    }
                });
            });

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                fetchResults();
            });
            
            container.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href && link.href.includes('page=')) {
                    e.preventDefault();
                    
                    container.style.opacity = '0.5';
                    fetch(link.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContainer = doc.getElementById('result-container');
                        if (newContainer) {
                            container.innerHTML = newContainer.innerHTML;
                        }
                        container.style.opacity = '1';
                        window.history.pushState({}, '', link.href);
                        container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    });
                }
            });
        }
    });
</script>
@endpush
