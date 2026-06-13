@extends('layouts.app')
@section('title', 'Edit Supplier: ' . $supplier->nama_supplier)
@section('page-title', 'Edit Supplier')
@section('page-subtitle', 'Ubah data supplier: ' . $supplier->nama_supplier)

@section('content')
<div class="max-w-xl animate-fade-in">
    <a href="{{ route('admin.suppliers.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 text-sm mb-5 transition-colors">
        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Daftar Supplier
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-surface-200 overflow-hidden">
        <div class="px-8 py-5 border-b border-surface-100 flex items-center gap-3">
            <div class="w-10 h-10 bg-violet-100 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-truck text-violet-600 text-lg"></i>
            </div>
            <div>
                <h2 class="font-bold text-slate-800">{{ $supplier->nama_supplier }}</h2>
                <p class="text-xs text-slate-400">{{ $supplier->stock_mutations_count ?? $supplier->stockMutations()->count() }} transaksi stok</p>
            </div>
        </div>

        <form action="{{ route('admin.suppliers.update', $supplier) }}" method="POST" class="px-8 py-6 space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="form-label">Nama Supplier <span class="text-red-500">*</span></label>
                <input type="text" name="nama_supplier" value="{{ old('nama_supplier', $supplier->nama_supplier) }}"
                    class="form-input {{ $errors->has('nama_supplier') ? 'error' : '' }}">
                @error('nama_supplier') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Nomor Kontak</label>
                    <input type="text" name="kontak" value="{{ old('kontak', $supplier->kontak) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $supplier->email) }}"
                        class="form-input {{ $errors->has('email') ? 'error' : '' }}">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="form-label">Alamat</label>
                <textarea name="alamat" rows="3" class="form-input resize-none">{{ old('alamat', $supplier->alamat) }}</textarea>
            </div>

            <div>
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan" value="{{ old('keterangan', $supplier->keterangan) }}" class="form-input">
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600 border-slate-300 rounded cursor-pointer">
                <label for="is_active" class="text-sm text-slate-700 font-medium cursor-pointer">Supplier Aktif</label>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-6 py-3 rounded-xl shadow-sm hover:-translate-y-0.5 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.suppliers.index') }}" class="text-slate-500 hover:text-slate-700 text-sm px-4 py-3 rounded-xl hover:bg-slate-100 transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>

<style>
.form-label { display:block; font-size:.875rem; font-weight:600; color:rgb(51,65,85); margin-bottom:.375rem; }
.form-input  { width:100%; border:1px solid rgb(226,232,240); border-radius:.75rem; padding:.625rem 1rem; font-size:.875rem; color:rgb(30,41,59); background:rgb(248,250,252); outline:none; transition:all .2s; }
.form-input:focus { background:white; border-color:rgb(96,165,250); box-shadow:0 0 0 4px rgb(219,234,254); }
.form-input.error { border-color:rgb(248,113,113); }
.form-error { margin-top:.375rem; font-size:.75rem; color:rgb(220,38,38); display:flex; align-items:center; gap:.25rem; }
</style>
@endsection
