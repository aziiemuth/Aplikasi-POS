@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('page-subtitle', 'Kelola akun kasir dan admin')

@section('content')
<div class="space-y-5 animate-fade-in">

    {{-- ===== HEADER BAR ===== --}}
    <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
        {{-- Search & Filter --}}
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex items-center gap-2">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama, username, email..."
                    class="pl-9 pr-4 py-2.5 text-sm border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all w-64">
            </div>
            <select name="role" class="text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:ring-2 focus:ring-blue-100 outline-none transition-all">
                <option value="">Semua Role</option>
                <option value="admin"  {{ request('role') == 'admin'  ? 'selected' : '' }}>Admin</option>
                <option value="kasir"  {{ request('role') == 'kasir'  ? 'selected' : '' }}>Kasir</option>
            </select>
            <button type="submit" class="bg-slate-700 text-white text-sm px-4 py-2.5 rounded-xl hover:bg-slate-800 transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search','role']))
            <a href="{{ route('admin.users.index') }}" class="text-slate-500 text-sm px-3 py-2.5 rounded-xl hover:bg-slate-100 transition-colors flex items-center gap-1">
                <i class="fa-solid fa-xmark"></i> Reset
            </a>
            @endif
        </form>

        {{-- Tambah User --}}
        <a href="{{ route('admin.users.create') }}"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm shadow-blue-500/30 hover:shadow-blue-500/40 transition-all hover:-translate-y-0.5">
            <i class="fa-solid fa-user-plus"></i>
            Tambah User
        </a>
    </div>

    {{-- ===== TABLE ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-surface-200 overflow-hidden">
        <table class="w-full text-sm">
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
                                        class="p-2 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors">
                                        <i class="fa-solid fa-rotate-left text-sm"></i>
                                    </button>
                                </form>
                            @else
                                {{-- Edit --}}
                                <a href="{{ route('admin.users.edit', $user) }}" title="Edit"
                                    class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>
                                {{-- Toggle Status --}}
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        class="p-2 {{ $user->is_active ? 'text-amber-600 hover:bg-amber-100' : 'text-emerald-600 hover:bg-emerald-100' }} rounded-lg transition-colors">
                                        <i class="fa-solid {{ $user->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} text-sm"></i>
                                    </button>
                                </form>
                                {{-- Hapus --}}
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" id="del-user-{{ $user->id }}">
                                    @csrf @method('DELETE')
                                    <button type="button" title="Hapus"
                                        onclick="confirmDelete('del-user-{{ $user->id }}', '{{ $user->name }}')"
                                        class="p-2 text-red-500 hover:bg-red-100 rounded-lg transition-colors">
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

        {{-- Pagination --}}
        @if($query->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $query->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
