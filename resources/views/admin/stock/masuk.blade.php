@extends('layouts.app')
@section('title', 'Stok Masuk')
@section('page-title', 'Stok Masuk')
@section('page-subtitle', 'Tambah stok produk — HPP akan dihitung ulang otomatis (rata-rata tertimbang)')

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
<div class="max-w-2xl animate-fade-in">
    <a href="{{ route('admin.stock.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 text-sm mb-5 transition-colors">
        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Riwayat Mutasi
    </a>

    <div class="bg-white rounded-2xl shadow-sm border border-surface-200 overflow-hidden">
        <div class="px-8 py-5 border-b border-surface-100 bg-emerald-50 flex items-center gap-3">
            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-arrow-down-to-bracket text-emerald-600 text-lg"></i>
            </div>
            <div>
                <h2 class="font-bold text-slate-800">Form Stok Masuk</h2>
                <p class="text-xs text-slate-500">HPP akan otomatis dihitung ulang dengan metode rata-rata tertimbang</p>
            </div>
        </div>

        {{-- Info FASE 3.4 --}}
        <div class="px-8 pt-5">
            <div class="bg-violet-50 border border-violet-200 rounded-xl px-4 py-3 text-xs text-violet-800 flex items-start gap-2">
                <i class="fa-solid fa-calculator text-violet-500 mt-0.5 shrink-0"></i>
                <div>
                    <p class="font-semibold mb-0.5">Fase 3.4 — HPP Rata-rata Tertimbang Otomatis</p>
                    <p>HPP Baru = ((Stok Lama × HPP Lama) + (Qty Masuk × Harga Beli)) ÷ Total Stok</p>
                    <p class="mt-1">Jika harga beli dikosongkan → HPP tidak berubah.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.stock.masuk.store') }}" method="POST" class="px-8 py-6 space-y-5">
            @csrf

            {{-- Produk --}}
            <div>
                <label class="form-label">Pilih Produk <span class="text-red-500">*</span></label>
                <select name="product_id" id="product-select" class="form-input {{ $errors->has('product_id') ? 'error' : '' }}"
                    onchange="loadProductInfo(this.value)">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}"
                        data-stok="{{ $p->stok_saat_ini }}"
                        data-satuan="{{ $p->satuan }}"
                        data-hpp="{{ $p->modal_hpp }}"
                        {{ (old('product_id', request('product_id')) == $p->id) ? 'selected' : '' }}>
                        {{ $p->nama_produk }}
                    </option>
                    @endforeach
                </select>
                @error('product_id') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror

                {{-- Info stok produk yang dipilih --}}
                <div id="product-info" class="hidden mt-2 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                    <div class="flex gap-6 text-xs">
                        <div>
                            <p class="text-slate-400">Stok Saat Ini</p>
                            <p class="font-bold text-slate-800" id="info-stok">—</p>
                        </div>
                        <div>
                            <p class="text-slate-400">HPP Saat Ini</p>
                            <p class="font-bold text-violet-700" id="info-hpp">—</p>
                        </div>
                        <div>
                            <p class="text-slate-400">HPP Baru (preview)</p>
                            <p class="font-bold text-emerald-700" id="info-hpp-baru">—</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Jumlah + Harga Beli --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Jumlah Masuk <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', 1) }}" min="1"
                        class="form-input {{ $errors->has('jumlah') ? 'error' : '' }}"
                        oninput="hitungHppBaru()">
                    @error('jumlah') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="form-label">
                        Harga Beli / unit
                        <span class="font-normal text-slate-400">(opsional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Rp</span>
                        <input type="number" name="harga_beli" id="harga-beli" value="{{ old('harga_beli') }}" min="0" step="100"
                            placeholder="0"
                            class="form-input pl-10"
                            oninput="hitungHppBaru()">
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Kosongkan jika tidak ingin mengubah HPP</p>
                </div>
            </div>

            {{-- Supplier --}}
            <div>
                <label class="form-label">Supplier / Pemasok <span class="font-normal text-slate-400">(opsional)</span></label>
                <select name="supplier_id" class="form-input">
                    <option value="">-- Tanpa Supplier --</option>
                    @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->nama_supplier }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="form-label">Keterangan</label>
                <input type="text" name="keterangan" value="{{ old('keterangan') }}"
                    placeholder="Contoh: Restock bulanan dari distributor"
                    class="form-input">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm px-6 py-3 rounded-xl shadow-sm hover:-translate-y-0.5 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Stok Masuk
                </button>
                <a href="{{ route('admin.stock.index') }}" class="text-slate-500 hover:text-slate-700 text-sm px-4 py-3 rounded-xl hover:bg-slate-100 transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentProduct = { stok: 0, hpp: 0 };

function loadProductInfo(productId) {
    const select = document.getElementById('product-select');
    const opt = select.options[select.selectedIndex];

    if (!productId || !opt.dataset.stok) {
        document.getElementById('product-info').classList.add('hidden');
        return;
    }

    currentProduct = {
        stok: parseFloat(opt.dataset.stok) || 0,
        hpp:  parseFloat(opt.dataset.hpp) || 0,
        satuan: opt.dataset.satuan
    };

    document.getElementById('info-stok').textContent = currentProduct.stok + ' ' + currentProduct.satuan;
    document.getElementById('info-hpp').textContent = 'Rp ' + currentProduct.hpp.toLocaleString('id-ID');
    document.getElementById('product-info').classList.remove('hidden');
    hitungHppBaru();
}

// === FASE 3.4: Preview HPP Rata-rata Tertimbang Langsung di Form ===
function hitungHppBaru() {
    const jumlah    = parseFloat(document.getElementById('jumlah').value) || 0;
    const hargaBeli = parseFloat(document.getElementById('harga-beli').value) || 0;
    const infoEl    = document.getElementById('info-hpp-baru');

    if (!currentProduct.stok && !jumlah) { infoEl.textContent = '—'; return; }

    if (hargaBeli <= 0) {
        infoEl.textContent = 'Tidak berubah';
        infoEl.className = 'font-bold text-slate-500';
        return;
    }

    let hppBaru;
    if (currentProduct.stok <= 0) {
        hppBaru = hargaBeli;
    } else {
        hppBaru = ((currentProduct.stok * currentProduct.hpp) + (jumlah * hargaBeli)) / (currentProduct.stok + jumlah);
    }

    infoEl.textContent = 'Rp ' + Math.round(hppBaru).toLocaleString('id-ID');
    infoEl.className = 'font-bold text-emerald-700';
}

// Load jika ada pre-selected product (dari URL)
window.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('product-select');
    if (sel.value) loadProductInfo(sel.value);
});
</script>
@endpush
