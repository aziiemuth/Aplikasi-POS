@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('page-subtitle', 'Kelola akun kasir dan admin')

@section('content')
<div class="space-y-5 animate-fade-in">

    {{-- ===== HEADER BAR ===== --}}
    <div class="flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
        {{-- Search & Filter --}}
        <form id="filter-form" action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama, username, email..."
                    class="pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all w-full md:w-64">
            </div>
            <div class="flex items-center gap-2">
                <select name="role" class="text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all flex-1 sm:flex-none">
                    <option value="">Semua Role</option>
                    <option value="admin"  {{ request('role') == 'admin'  ? 'selected' : '' }}>Admin</option>
                    <option value="kasir"  {{ request('role') == 'kasir'  ? 'selected' : '' }}>Kasir</option>
                </select>
                <button type="submit" class="hidden bg-slate-700 text-white text-sm px-4 py-2.5 rounded-xl hover:bg-slate-800 transition-colors font-semibold">
                    Filter
                </button>
                @if(request()->hasAny(['search','role']))
                <a href="{{ route('admin.users.index') }}" class="text-slate-500 text-sm px-3 py-2.5 rounded-xl hover:bg-slate-100 transition-colors flex items-center gap-1">
                    <i class="fa-solid fa-xmark"></i> Reset
                </a>
                @endif
            </div>
        </form>

        {{-- Tambah User --}}
        <a href="{{ route('admin.users.create') }}"
            class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm shadow-blue-500/30 hover:shadow-blue-500/40 transition-all hover:-translate-y-0.5">
            <i class="fa-solid fa-user-plus"></i>
            Tambah User
        </a>
    </div>

    {{-- ===== TABLE ===== --}}
    <div id="result-container" class="bg-white rounded-2xl shadow-sm border border-surface-200 overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-sm min-w-[800px]">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">User</th>
                    <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Username</th>
                    <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Role</th>
                    <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Dibuat</th>
                    <th class="text-center px-4 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($query as $user)
                <tr class="hover:bg-slate-50/50 transition-colors {{ $user->trashed() ? 'opacity-50 bg-red-50/30' : '' }}">
                    {{-- Avatar + Name + Email --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full {{ $user->isAdmin() ? 'bg-amber-500' : 'bg-blue-600' }} flex items-center justify-center text-white font-bold text-sm shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                                <p class="text-xs text-slate-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    {{-- Username --}}
                    <td class="px-4 py-4">
                        <span class="font-mono text-sm text-slate-700 bg-slate-100 px-2 py-1 rounded-lg">{{ $user->username }}</span>
                    </td>
                    {{-- Role Badge --}}
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full
                            {{ $user->isAdmin() ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                            <i class="fa-solid {{ $user->isAdmin() ? 'fa-shield-halved' : 'fa-user-tie' }} text-xs"></i>
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    {{-- Status --}}
                    <td class="px-4 py-4">
                        @if($user->trashed())
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-red-100 text-red-700">
                                <i class="fa-solid fa-trash text-xs"></i> Dihapus
                            </span>
                        @elseif($user->is_active)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">
                                <i class="fa-solid fa-circle text-xs"></i> Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">
                                <i class="fa-solid fa-circle text-xs"></i> Nonaktif
                            </span>
                        @endif
                    </td>
                    {{-- Tanggal --}}
                    <td class="px-4 py-4 text-xs text-slate-400">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    {{-- Actions --}}
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center gap-1.5">
                            @if($user->trashed())
                                {{-- Restore --}}
                                <form action="{{ route('admin.users.restore', $user->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="Pulihkan"
                                        class="p-2 bg-emerald-50 border border-emerald-100 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors">
                                        <i class="fa-solid fa-rotate-left text-sm"></i>
                                    </button>
                                </form>
                            @else
                                {{-- Edit --}}
                                <a href="{{ route('admin.users.edit', $user) }}" title="Edit"
                                    class="p-2 bg-blue-50 border border-blue-100 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                {{-- Toggle Status --}}
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        class="p-2 {{ $user->is_active ? 'bg-amber-50 border border-amber-100 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 border border-emerald-100 text-emerald-600 hover:bg-emerald-100' }} rounded-lg transition-colors">
                                        <i class="fa-solid {{ $user->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} text-sm"></i>
                                    </button>
                                </form>
                                {{-- Hapus --}}
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" id="del-user-{{ $user->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" title="Hapus"
                                        onclick="confirmDelete('del-user-{{ $user->id }}', '{{ $user->name }}')"
                                        class="p-2 bg-red-50 border border-red-100 text-red-500 hover:bg-red-100 rounded-lg transition-colors">
                                        <i class="fa-solid fa-trash text-sm"></i>
                                    </button>
                                </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <i class="fa-solid fa-users-slash text-slate-200 text-4xl mb-3"></i>
                        <p class="text-slate-400 font-medium">Tidak ada user ditemukan</p>
                        <p class="text-slate-300 text-xs mt-1">Coba ubah filter pencarian Anda</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        </div>

        {{-- Pagination --}}
        @if($query->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $query->links() }}
        </div>
        @endif
    </div>
</div>

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
@endsection
