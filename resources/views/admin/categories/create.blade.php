@extends('layouts.app')
@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')
@section('page-subtitle', 'Buat kelompok produk baru')

@section('content')
<div class="max-w-lg animate-fade-in">
    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 text-sm mb-5 transition-colors">
        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Daftar Kategori
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-surface-200 overflow-hidden">
        <div class="px-8 py-5 border-b border-surface-100 flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-plus text-blue-600"></i>
            </div>
            <div>
                <h2 class="font-bold text-slate-800">Tambah Kategori Baru</h2>
                <p class="text-xs text-slate-400">Kelompok untuk filter produk di POS</p>
            </div>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" class="px-8 py-6 space-y-5">
            @csrf

            <div>
                <label class="form-label">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}" placeholder="Contoh: Minuman Dingin"
                    class="form-input {{ $errors->has('nama_kategori') ? 'error' : '' }}">
                @error('nama_kategori') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
            </div>

            <div>
                <label class="form-label">Deskripsi</label>
                <input type="text" name="deskripsi" value="{{ old('deskripsi') }}" placeholder="Deskripsi singkat (opsional)"
                    class="form-input">
            </div>

            {{-- Icon Picker --}}
            <div>
                <label class="form-label">Icon FontAwesome</label>
                <div class="flex gap-2">
                    <input type="text" name="icon" id="icon-input" value="{{ old('icon', 'fa-solid fa-tag') }}"
                        placeholder="fa-solid fa-tag"
                        class="form-input flex-1">
                    <div class="w-11 h-11 border border-slate-200 rounded-xl flex items-center justify-center bg-blue-50 flex-shrink-0">
                        <i id="icon-preview" class="{{ old('icon', 'fa-solid fa-tag') }} text-blue-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-slate-400 mt-1">Cari icon di <a href="https://fontawesome.com/icons" target="_blank" class="text-blue-500 hover:underline">fontawesome.com/icons</a></p>
                {{-- Icon Quick Pick --}}
                <div class="flex flex-wrap gap-2 mt-2">
                    @foreach([
                        'fa-solid fa-wine-bottle', 'fa-solid fa-mug-hot', 'fa-solid fa-cookie',
                        'fa-solid fa-utensils', 'fa-solid fa-smoking', 'fa-solid fa-soap',
                        'fa-solid fa-box', 'fa-solid fa-plug', 'fa-solid fa-tag',
                        'fa-solid fa-shirt', 'fa-solid fa-mobile-screen', 'fa-solid fa-baby',
                    ] as $icon)
                    <button type="button" onclick="setIcon('{{ $icon }}')"
                        class="w-9 h-9 border border-slate-200 rounded-lg flex items-center justify-center hover:bg-blue-50 hover:border-blue-300 transition-colors text-slate-500 hover:text-blue-600"
                        title="{{ $icon }}">
                        <i class="{{ $icon }} text-sm"></i>
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', true) ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-400 cursor-pointer">
                <label for="is_active" class="text-sm text-slate-700 font-medium cursor-pointer">Kategori Aktif (tampil di POS)</label>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-6 py-3 rounded-xl shadow-sm hover:-translate-y-0.5 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Kategori
                </button>
                <a href="{{ route('admin.categories.index') }}" class="text-slate-500 hover:text-slate-700 text-sm px-4 py-3 rounded-xl hover:bg-slate-100 transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>

<style>
.form-label { display:block; font-size:.875rem; font-weight:600; color:rgb(51,65,85); margin-bottom:.375rem; }
.form-input  { width:100%; border:1px solid rgb(226,232,240); border-radius:.75rem; padding:.625rem 1rem; font-size:.875rem; color:rgb(30,41,59); background:rgb(248,250,252); outline:none; transition:all .2s; }
.form-input:focus { background:white; border-color:rgb(96,165,250); box-shadow:0 0 0 4px rgb(219,234,254); }
.form-input.error { border-color:rgb(248,113,113); background:rgb(254,242,242); }
.form-error { margin-top:.375rem; font-size:.75rem; color:rgb(220,38,38); display:flex; align-items:center; gap:.25rem; }
</style>

<script>
function setIcon(icon) {
    document.getElementById('icon-input').value = icon;
    document.getElementById('icon-preview').className = icon + ' text-blue-600 text-xl';
}

document.getElementById('icon-input').addEventListener('input', function() {
    document.getElementById('icon-preview').className = this.value + ' text-blue-600 text-xl';
});
</script>
@endsection
