@extends('layouts.app')
@section('title', 'Edit Produk: ' . $product->nama_produk)
@section('page-title', 'Edit Produk')
@section('page-subtitle', $product->nama_produk . ' — SKU: ' . $product->sku)

@push('styles')
<style>
.form-label { display:block; font-size:.875rem; font-weight:600; color:rgb(51,65,85); margin-bottom:.375rem; }
.form-input  { width:100%; border:1px solid rgb(226,232,240); border-radius:.75rem; padding:.625rem 1rem; font-size:.875rem; color:rgb(30,41,59); background:rgb(248,250,252); outline:none; transition:all .2s; }
.form-input:focus { background:white; border-color:rgb(96,165,250); box-shadow:0 0 0 4px rgb(219,234,254); }
.form-input.error { border-color:rgb(248,113,113); background:rgb(254,242,242); }
.form-error { margin-top:.375rem; font-size:.75rem; color:rgb(220,38,38); display:flex; align-items:center; gap:.25rem; }
</style>
@endpush

@section('content')
<div class="max-w-3xl animate-fade-in">
    <a href="{{ route('admin.products.show', $product) }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 text-sm mb-5 transition-colors">
        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Detail Produk
    </a>

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Foto --}}
        <div class="space-y-5">
            <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-5">
                <h3 class="font-bold text-slate-800 text-sm mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-image text-blue-500"></i> Foto Produk
                </h3>

                <div class="w-full aspect-square rounded-xl border border-slate-200 overflow-hidden mb-3 bg-slate-50 flex items-center justify-center cursor-pointer"
                    onclick="document.getElementById('foto').click()">
                    @if($product->foto)
                        <img id="foto-preview" src="{{ Storage::url($product->foto) }}" class="w-full h-full object-contain" alt="">
                    @else
                        <img id="foto-preview" src="" class="hidden w-full h-full object-contain">
                        <i class="fa-solid fa-box text-slate-200 text-5xl" id="foto-placeholder"></i>
                    @endif
                </div>

                <input type="file" name="foto" id="foto" accept="image/*" class="hidden" onchange="previewFoto(this)">

                <div class="space-y-2">
                    <button type="button" onclick="document.getElementById('foto').click()"
                        class="w-full text-xs text-blue-600 border border-blue-200 rounded-xl py-2 hover:bg-blue-50 transition-colors">
                        <i class="fa-solid fa-upload mr-1"></i> Ganti Foto
                    </button>
                    @if($product->foto)
                    {{-- Hapus foto via fetch() agar tidak nested form --}}
                    <button type="button" onclick="hapusFotoProduk()"
                        class="w-full text-xs text-red-500 border border-red-200 rounded-xl py-2 hover:bg-red-50 transition-colors">
                        <i class="fa-solid fa-trash mr-1"></i> Hapus Foto
                    </button>
                    @endif
                </div>
            </div>

            {{-- SKU (readonly saat edit) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-5">
                <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-barcode text-violet-500"></i> SKU / Barcode
                </h3>
                <div class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-mono text-sm text-slate-700 break-all mb-2">
                    {{ $product->sku }}
                </div>
                <input type="hidden" name="sku" value="{{ $product->sku }}">
                <p class="text-xs text-slate-400">SKU tidak dapat diubah setelah produk dibuat (menjaga konsistensi transaksi).</p>
            </div>
        </div>

        {{-- Detail --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6 space-y-4">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 pb-2 border-b border-slate-100">
                    <i class="fa-solid fa-info-circle text-blue-500"></i> Informasi Dasar
                </h3>
                <div>
                    <label class="form-label">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_produk" value="{{ old('nama_produk', $product->nama_produk) }}"
                        class="form-input {{ $errors->has('nama_produk') ? 'error' : '' }}">
                    @error('nama_produk') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-input">
                            <option value="">Tanpa Kategori</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Satuan <span class="text-red-500">*</span></label>
                        <select name="satuan" class="form-input">
                            @foreach($satuan as $s)
                            <option value="{{ $s }}" {{ old('satuan', $product->satuan) == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" class="form-input resize-none">{{ old('deskripsi', $product->deskripsi) }}</textarea>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6 space-y-4">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 pb-2 border-b border-slate-100">
                    <i class="fa-solid fa-money-bill-wave text-emerald-500"></i> Harga & Stok
                </h3>

                {{-- Info HPP dihitung otomatis --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5 text-xs text-amber-800 flex items-start gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5"></i>
                    <p><strong>Catatan:</strong> HPP otomatis diperbarui oleh sistem saat ada <strong>Stok Masuk</strong> menggunakan metode rata-rata tertimbang. Ubah HPP di sini hanya jika diperlukan koreksi manual.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Modal / HPP <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Rp</span>
                            <input type="number" name="modal_hpp" value="{{ old('modal_hpp', $product->modal_hpp) }}" min="0" step="100"
                                class="form-input pl-10" oninput="hitungLaba()">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Harga Jual <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Rp</span>
                            <input type="number" name="harga_jual" value="{{ old('harga_jual', $product->harga_jual) }}" min="0" step="100"
                                class="form-input pl-10" oninput="hitungLaba()">
                        </div>
                    </div>
                </div>

                <div id="laba-info" class="hidden bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 flex items-center gap-3">
                    <i class="fa-solid fa-chart-line text-emerald-500"></i>
                    <div class="text-sm">
                        <span class="text-slate-600">Laba per unit: </span>
                        <span id="laba-amount" class="font-bold text-emerald-700">Rp 0</span>
                        <span id="laba-pct" class="text-emerald-500 ml-2 text-xs">(0%)</span>
                    </div>
                </div>

                <div>
                    <label class="form-label">Stok Minimum (Alert)</label>
                    <input type="number" name="stok_minimum" value="{{ old('stok_minimum', $product->stok_minimum) }}" min="0"
                        class="form-input">
                    <p class="text-xs text-slate-400 mt-1">Stok saat ini: <strong>{{ $product->stok_saat_ini }} {{ $product->satuan }}</strong> — ubah via Stok Masuk/Keluar</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-5">
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                        {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 border-slate-300 rounded cursor-pointer">
                    <label for="is_active" class="text-sm text-slate-700 font-medium cursor-pointer">Produk Aktif (tampil di POS)</label>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-8 py-3 rounded-xl shadow-sm hover:-translate-y-0.5 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
                <a href="{{ route('admin.products.show', $product) }}" class="text-slate-500 text-sm px-4 py-3 rounded-xl hover:bg-slate-100 transition-colors">Batal</a>
            </div>
        </div>
    </div>
    </form>
</div>

@push('scripts')
<script>
function previewFoto(input) {
    if (input.files && input.files[0]) {
        const r = new FileReader();
        r.onload = e => {
            document.getElementById('foto-preview').src = e.target.result;
            document.getElementById('foto-preview').classList.remove('hidden');
            const ph = document.getElementById('foto-placeholder');
            if(ph) ph.classList.add('hidden');
        };
        r.readAsDataURL(input.files[0]);
    }
}
function hitungLaba() {
    const hpp = parseFloat(document.querySelector('[name=modal_hpp]').value) || 0;
    const jual = parseFloat(document.querySelector('[name=harga_jual]').value) || 0;
    const laba = jual - hpp;
    const pct = hpp > 0 ? ((laba/hpp)*100).toFixed(1) : 0;
    const box = document.getElementById('laba-info');
    if(jual > 0 || hpp > 0) {
        box.classList.remove('hidden');
        document.getElementById('laba-amount').textContent = 'Rp '+laba.toLocaleString('id-ID');
        document.getElementById('laba-pct').textContent = '('+pct+'%)';
        document.getElementById('laba-amount').className = 'font-bold '+(laba>=0?'text-emerald-700':'text-red-600');
    }
}
window.addEventListener('DOMContentLoaded', hitungLaba);

// === Hapus foto via fetch (tanpa nested form) ===
function hapusFotoProduk() {
    Swal.fire({
        title: 'Hapus Foto?',
        text: 'Foto produk akan dihapus dari server.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        width: '380px',
        customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl', cancelButton: 'rounded-xl' }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("admin.products.destroy-foto", $product) }}', {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            }).then(() => {
                window.location.reload();
            }).catch(() => {
                Toast.fire({ icon: 'error', title: 'Gagal menghapus foto' });
            });
        }
    });
}
</script>
@endpush
@endsection
