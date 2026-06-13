@extends('layouts.app')

@section('title', 'Edit User: ' . $user->name)
@section('page-title', 'Edit User')
@section('page-subtitle', 'Ubah data akun: ' . $user->username)

@section('content')
<div class="max-w-2xl animate-fade-in">

    {{-- Back --}}
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 text-sm mb-5 transition-colors">
        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Daftar User
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-surface-200 overflow-hidden">
        <div class="px-8 py-5 border-b border-surface-100 flex items-center gap-3">
            <div class="w-10 h-10 {{ $user->isAdmin() ? 'bg-amber-500' : 'bg-blue-600' }} rounded-xl flex items-center justify-center text-white font-bold text-sm">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="font-bold text-slate-800">{{ $user->name }}</h2>
                <p class="text-xs text-slate-400">{{ $user->username }} &mdash; {{ ucfirst($user->role) }}</p>
            </div>
        </div>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="px-8 py-6 space-y-5">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="form-input {{ $errors->has('name') ? 'error' : '' }}">
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Username + Email --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}"
                        class="form-input {{ $errors->has('username') ? 'error' : '' }}">
                    @error('username') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="form-input {{ $errors->has('email') ? 'error' : '' }}">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Phone + Role --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nomor HP</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="form-input {{ $errors->has('phone') ? 'error' : '' }}">
                    @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Role <span class="text-red-500">*</span></label>
                    <select name="role" class="form-input {{ $errors->has('role') ? 'error' : '' }}"
                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                        <option value="kasir" {{ old('role', $user->role) == 'kasir' ? 'selected' : '' }}>👤 Kasir</option>
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>🛡️ Admin</option>
                    </select>
                    @if($user->id === auth()->id())
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <p class="mt-1 text-xs text-amber-600 flex items-center gap-1">
                        <i class="fa-solid fa-triangle-exclamation"></i> Anda tidak bisa mengubah role akun Anda sendiri.
                    </p>
                    @endif
                    @error('role') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Password (opsional saat edit) --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Password Baru <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                        class="form-input {{ $errors->has('password') ? 'error' : '' }}">
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                        class="form-input">
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-6 py-3 rounded-xl shadow-sm hover:-translate-y-0.5 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.users.index') }}"
                    class="text-slate-500 hover:text-slate-700 text-sm px-4 py-3 rounded-xl hover:bg-slate-100 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<style type="text/tailwindcss">
.form-label { @apply block text-sm font-semibold text-slate-700 mb-1.5; }
.form-input  { @apply w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 bg-slate-50 focus:bg-white focus:border-blue-400 focus:ring-4 focus:ring-blue-100 outline-none transition-all; }
.form-input.error { @apply border-red-400 bg-red-50 focus:ring-red-100; }
.form-error  { @apply mt-1 text-xs text-red-600 flex items-center gap-1; }
</style>
@endsection
