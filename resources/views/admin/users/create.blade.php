@extends('layouts.app')

@section('title', 'Tambah User Baru')
@section('page-title', 'Tambah User Baru')
@section('page-subtitle', 'Buat akun kasir atau admin baru')

@section('content')
<div class="max-w-2xl animate-fade-in">

    {{-- Back --}}
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 text-sm mb-5 transition-colors">
        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Daftar User
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-surface-200 overflow-hidden">
        <div class="px-8 py-5 border-b border-surface-100 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-user-plus text-blue-600"></i>
            </div>
            <div>
                <h2 class="font-bold text-slate-800">Tambah Akun Baru</h2>
                <p class="text-xs text-slate-400">Isi semua field yang diperlukan</p>
            </div>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="px-8 py-6 space-y-5">
            @csrf

            {{-- Name --}}
            <div>
                <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap staf"
                    class="form-input {{ $errors->has('name') ? 'error' : '' }}">
                @error('name') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Username + Email --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="username123"
                        class="form-input {{ $errors->has('username') ? 'error' : '' }}">
                    @error('username') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@toko.com"
                        class="form-input {{ $errors->has('email') ? 'error' : '' }}">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Phone + Role --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nomor HP</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx"
                        class="form-input {{ $errors->has('phone') ? 'error' : '' }}">
                    @error('phone') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Role <span class="text-red-500">*</span></label>
                    <select name="role" class="form-input {{ $errors->has('role') ? 'error' : '' }}">
                        <option value="">Pilih Role...</option>
                        <option value="kasir" {{ old('role') == 'kasir' ? 'selected' : '' }}>
                            👤 Kasir
                        </option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                            🛡️ Admin
                        </option>
                    </select>
                    @error('role') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Password + Confirm --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" placeholder="Min. 8 karakter"
                        class="form-input {{ $errors->has('password') ? 'error' : '' }}">
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password"
                        class="form-input">
                </div>
            </div>

            {{-- Info --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-700 flex items-start gap-2">
                <i class="fa-solid fa-circle-info mt-0.5 shrink-0"></i>
                <p>Kasir <strong>hanya</strong> bisa mengakses layar POS. Admin memiliki akses penuh ke semua fitur sistem termasuk laporan dan master data.</p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-6 py-3 rounded-xl shadow-sm hover:-translate-y-0.5 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan User
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
.form-input select { appearance: none; }
.form-error  { @apply mt-1 text-xs text-red-600 flex items-center gap-1; }
</style>
@endsection
