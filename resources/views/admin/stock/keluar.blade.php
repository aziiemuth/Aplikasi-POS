@extends('layouts.app')
@section('title', 'Stok Keluar Manual')
@section('page-title', 'Stok Keluar Manual')
@section('page-subtitle', 'Penyesuaian stok manual — hanya untuk barang rusak, hilang, atau koreksi (Admin Only)')

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
        <div class="px-8 py-5 border-b border-surface-100 bg-rose-50 flex items-center gap-3">
            <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-arrow-up-from-bracket text-rose-600 text-lg"></i>
            </div>
            <div>
                <h2 class="font-bold text-slate-800">Form Stok Keluar Manual</h2>
                <p class="text-xs text-slate-500">Hanya untuk penyesuaian manual oleh Admin</p>
            </div>
        </div>

        {{-- Peringatan --}}
        <div class="px-8 pt-5">
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-xs text-amber-800 flex items-start gap-2">
                <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5 shrink-0"></i>
                <div>
                    <p class="font-semibold mb-0.5">Stok Keluar Dikendalikan Ketat</p>
                    <p>Stok keluar karena <strong>penjualan</strong> dilakukan otomatis oleh sistem POS saat transaksi checkout.
                    Halaman ini hanya untuk koreksi manual seperti barang rusak, hilang, atau kesalahan input.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.stock.keluar.store') }}" method="POST" class="px-8 py-6 space-y-5">
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
                        {{ (old('product_id', request('product_id')) == $p->id) ? 'selected' : '' }}>
                        {{ $p->nama_produk }} (Stok: {{ $p->stok_saat_ini }} {{ $p->satuan }})
                    </option>
                    @endforeach
                </select>
                @error('product_id') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror

                {{-- Info stok --}}
                <div id="product-info" class="hidden mt-2 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                    <div class="flex gap-6 text-xs">
                        <div>
                            <p class="text-slate-400">Stok Saat Ini</p>
                            <p class="font-bold text-slate-800 text-lg" id="info-stok">—</p>
                        </div>
                        <div>
                            <p class="text-slate-400">Stok Setelah Keluar</p>
                            <p class="font-bold text-rose-600 text-lg" id="info-stok-baru">—</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Jumlah --}}
            <div>
                <label class="form-label">Jumlah Keluar <span class="text-red-500">*</span></label>
                <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', 1) }}" min="1"
                    class="form-input {{ $errors->has('jumlah') ? 'error' : '' }}"
                    oninput="hitungStokBaru()">
                @error('jumlah') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
            </div>

            {{-- Alasan (Wajib untuk stok keluar) --}}
            <div>
                <label class="form-label">Alasan / Keterangan <span class="text-red-500">*</span></label>
                <select name="keterangan" class="form-input {{ $errors->has('keterangan') ? 'error' : '' }}">
                    <option value="">-- Pilih Alasan --</option>
                    <option {{ old('keterangan') == 'Barang rusak/tidak layak jual' ? 'selected':'' }}>Barang rusak/tidak layak jual</option>
                    <option {{ old('keterangan') == 'Barang hilang' ? 'selected':'' }}>Barang hilang</option>
                    <option {{ old('keterangan') == 'Kadaluarsa' ? 'selected':'' }}>Kadaluarsa</option>
                    <option {{ old('keterangan') == 'Koreksi input stok' ? 'selected':'' }}>Koreksi input stok</option>
                    <option {{ old('keterangan') == 'Sample / Tester' ? 'selected':'' }}>Sample / Tester</option>
                    <option {{ old('keterangan') == 'Retur ke supplier' ? 'selected':'' }}>Retur ke supplier</option>
                </select>
                @error('keterangan') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-rose-600 hover:bg-rose-700 text-white font-semibold text-sm px-6 py-3 rounded-xl shadow-sm hover:-translate-y-0.5 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Stok Keluar
                </button>
                <a href="{{ route('admin.stock.index') }}" class="text-slate-500 hover:text-slate-700 text-sm px-4 py-3 rounded-xl hover:bg-slate-100 transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentStok = 0, currentSatuan = '';

function loadProductInfo(productId) {
    const sel = document.getElementById('product-select');
    const opt = sel.options[sel.selectedIndex];
    if (!productId || !opt.dataset.stok) {
        document.getElementById('product-info').classList.add('hidden');
        return;
    }
    currentStok = parseFloat(opt.dataset.stok) || 0;
    currentSatuan = opt.dataset.satuan;
    document.getElementById('info-stok').textContent = currentStok + ' ' + currentSatuan;
    document.getElementById('product-info').classList.remove('hidden');
    hitungStokBaru();
}

function hitungStokBaru() {
    const jumlah = parseFloat(document.getElementById('jumlah').value) || 0;
    const sisaEl = document.getElementById('info-stok-baru');
    const sisa = currentStok - jumlah;
    sisaEl.textContent = sisa + ' ' + currentSatuan;
    sisaEl.className = 'font-bold text-lg ' + (sisa < 0 ? 'text-red-700' : sisa === 0 ? 'text-amber-600' : 'text-rose-600');
}

window.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('product-select');
    if (sel.value) loadProductInfo(sel.value);
});
</script>
@endpush
