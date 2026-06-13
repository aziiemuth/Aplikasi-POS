@extends('layouts.app')
@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk Baru')
@section('page-subtitle', 'Daftarkan produk baru ke katalog toko')

@push('styles')
<style>
.form-label { display:block; font-size:.875rem; font-weight:600; color:rgb(51,65,85); margin-bottom:.375rem; }
.form-input  { width:100%; border:1px solid rgb(226,232,240); border-radius:.75rem; padding:.625rem 1rem; font-size:.875rem; color:rgb(30,41,59); background:rgb(248,250,252); outline:none; transition:all .2s; }
.form-input:focus { background:white; border-color:rgb(96,165,250); box-shadow:0 0 0 4px rgb(219,234,254); }
.form-input.error { border-color:rgb(248,113,113); background:rgb(254,242,242); }
.form-error { margin-top:.375rem; font-size:.75rem; color:rgb(220,38,38); display:flex; align-items:center; gap:.25rem; }
#camera-view { width:100%; max-width:300px; border-radius:.75rem; }
#barcode-svg { max-width: 100%; height: auto; }
</style>
@endpush

@section('content')
<div class="max-w-3xl animate-fade-in">
    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-700 text-sm mb-5 transition-colors">
        <i class="fa-solid fa-arrow-left text-xs"></i> Kembali ke Daftar Produk
    </a>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="product-form">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ===== KOLOM KIRI: Foto & Barcode ===== --}}
        <div class="space-y-5">

            {{-- Upload Foto --}}
            <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-5">
                <h3 class="font-bold text-slate-800 text-sm mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-image text-blue-500"></i> Foto Produk
                </h3>
                <div id="foto-preview-box"
                    class="w-full aspect-square rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition-all mb-3 overflow-hidden bg-slate-50"
                    onclick="document.getElementById('foto').click()">
                    <img id="foto-preview" src="" alt="" class="hidden w-full h-full object-contain rounded-xl">
                    <div id="foto-placeholder" class="text-center p-4">
                        <i class="fa-solid fa-cloud-arrow-up text-slate-300 text-4xl mb-2"></i>
                        <p class="text-xs text-slate-400">Klik untuk upload</p>
                        <p class="text-xs text-slate-300 mt-1">JPG, PNG, WebP (max 2MB)</p>
                    </div>
                </div>
                <input type="file" name="foto" id="foto" accept="image/*" class="hidden" onchange="previewFoto(this)">
                <div class="text-center">
                    <button type="button" onclick="document.getElementById('foto').click()"
                        class="inline-flex px-6 text-xs text-slate-500 border border-slate-200 rounded-xl py-2 hover:bg-slate-50 transition-colors">
                        <i class="fa-solid fa-upload mr-1"></i> Pilih Foto
                    </button>
                </div>
            </div>

            {{-- === FASE 3.2: SKU / Barcode Generator === --}}
            <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-5">
                <h3 class="font-bold text-slate-800 text-sm mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-barcode text-violet-500"></i> SKU / Barcode
                </h3>

                <div class="relative mb-2">
                    <input type="text" name="sku" id="sku-input" value="{{ old('sku') }}"
                        placeholder="Masukkan atau generate..."
                        class="form-input pr-20 font-mono {{ $errors->has('sku') ? 'error' : '' }}"
                        maxlength="50">
                    {{-- Generate button --}}
                    <button type="button" id="btn-generate-sku" onclick="generateSku()"
                        title="Generate SKU unik 12-digit"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-xs bg-violet-600 text-white px-2.5 py-1.5 rounded-lg hover:bg-violet-700 transition-colors flex items-center gap-1">
                        <i class="fa-solid fa-bolt text-xs"></i> Auto
                    </button>
                </div>
                @error('sku') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
                <p class="text-xs text-slate-400 mb-3">Klik <strong>Auto</strong> untuk generate 12-digit unik, atau salin dari label fisik produk.</p>

                {{-- Preview Barcode Visual --}}
                <div id="barcode-preview-box" class="hidden bg-slate-50 border border-slate-200 rounded-xl p-3 text-center">
                    <svg id="barcode-svg"></svg>
                    <p class="text-xs text-slate-400 mt-1">Preview barcode</p>
                </div>

                {{-- === FASE 3.2: Scan via Kamera === --}}
                <div class="mt-3 pt-3 border-t border-slate-100 text-center">
                    <button type="button" id="btn-scan-camera" onclick="toggleCamera()"
                        class="inline-flex items-center justify-center px-5 gap-2 text-xs text-slate-600 border border-slate-200 rounded-xl py-2.5 hover:bg-slate-50 transition-colors">
                        <i class="fa-solid fa-camera text-blue-500"></i>
                        Scan via Kamera HP
                    </button>
                    <div id="camera-container" class="hidden mt-3 text-center">
                        <div id="reader" class="rounded-xl overflow-hidden mb-2"></div>
                        <button type="button" onclick="stopCamera()"
                            class="inline-flex items-center justify-center px-5 text-xs text-red-500 border border-red-200 rounded-xl py-2 hover:bg-red-50 transition-colors">
                            <i class="fa-solid fa-stop mr-1"></i> Stop Kamera
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== KOLOM KANAN: Detail Produk ===== --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Info Dasar --}}
            <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6 space-y-4">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 pb-2 border-b border-slate-100">
                    <i class="fa-solid fa-info-circle text-blue-500"></i> Informasi Dasar
                </h3>

                <div>
                    <label class="form-label">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_produk" value="{{ old('nama_produk') }}" placeholder="Nama lengkap produk"
                        class="form-input {{ $errors->has('nama_produk') ? 'error' : '' }}">
                    @error('nama_produk') <p class="form-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-input">
                            <option value="">Tanpa Kategori</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Satuan <span class="text-red-500">*</span></label>
                        <select name="satuan" class="form-input {{ $errors->has('satuan') ? 'error' : '' }}">
                            @foreach($satuan as $s)
                            <option value="{{ $s }}" {{ old('satuan') == $s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" placeholder="Deskripsi produk (opsional)"
                        class="form-input resize-none">{{ old('deskripsi') }}</textarea>
                </div>
            </div>

            {{-- Harga & Stok --}}
            <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6 space-y-4">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 pb-2 border-b border-slate-100">
                    <i class="fa-solid fa-money-bill-wave text-emerald-500"></i> Harga & Stok
                </h3>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">
                            Modal / HPP <span class="text-red-500">*</span>
                            <span class="font-normal text-slate-400">(Harga Beli)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium">Rp</span>
                            <input type="number" name="modal_hpp" value="{{ old('modal_hpp') }}" placeholder="0" min="0" step="100"
                                class="form-input pl-10 {{ $errors->has('modal_hpp') ? 'error' : '' }}"
                                oninput="hitungLaba()">
                        </div>
                        @error('modal_hpp') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">
                            Harga Jual <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm font-medium">Rp</span>
                            <input type="number" name="harga_jual" value="{{ old('harga_jual') }}" placeholder="0" min="0" step="100"
                                class="form-input pl-10 {{ $errors->has('harga_jual') ? 'error' : '' }}"
                                oninput="hitungLaba()">
                        </div>
                        @error('harga_jual') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Live Laba Kalkulator --}}
                <div id="laba-info" class="hidden bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 items-center gap-3">
                    <i class="fa-solid fa-chart-line text-emerald-500"></i>
                    <div class="text-sm">
                        <span class="text-slate-600">Laba per unit: </span>
                        <span id="laba-amount" class="font-bold text-emerald-700">Rp 0</span>
                        <span id="laba-pct" class="text-emerald-500 ml-2 text-xs">(0%)</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Stok Awal <span class="text-red-500">*</span></label>
                        <input type="number" name="stok_saat_ini" value="{{ old('stok_saat_ini') }}" placeholder="0" min="0"
                            class="form-input {{ $errors->has('stok_saat_ini') ? 'error' : '' }}">
                        @error('stok_saat_ini') <p class="form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="form-label">
                            Stok Minimum
                            <span class="font-normal text-slate-400">(Alert batas bawah)</span>
                        </label>
                        <input type="number" name="stok_minimum" value="{{ old('stok_minimum') }}" placeholder="5" min="0"
                            class="form-input">
                    </div>
                </div>
            </div>

            {{-- Pengaturan --}}
            <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
                    <i class="fa-solid fa-gear text-slate-500"></i> Pengaturan
                </h3>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                        class="w-4 h-4 text-blue-600 border-slate-300 rounded cursor-pointer">
                    <label for="is_active" class="text-sm text-slate-700 font-medium cursor-pointer">Produk Aktif (tampil di katalog POS)</label>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex flex-wrap items-center gap-3 pt-2">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm px-6 py-2.5 rounded-xl shadow-sm hover:-translate-y-0.5 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan
                </button>
                <a href="{{ route('admin.products.index') }}" class="text-slate-500 hover:text-slate-700 text-sm px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors">Batal</a>
            </div>
        </div>
    </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- JsBarcode — Fase 3.2: Preview Barcode Visual --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.6/JsBarcode.all.min.js"></script>
{{-- Html5-QrCode — Fase 3.2: Scan via Kamera --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrCode = null;

// ===== FASE 3.2: Generate SKU/Barcode unik 12-digit via AJAX =====
async function generateSku() {
    const btn = document.getElementById('btn-generate-sku');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i>';
    btn.disabled = true;

    try {
        const res = await fetch('{{ route('admin.products.generate-sku') }}', {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await res.json();
        document.getElementById('sku-input').value = data.sku;
        updateBarcodePreview(data.sku);

        Toast.fire({ icon: 'success', title: 'SKU berhasil digenerate!' });
    } catch (e) {
        Toast.fire({ icon: 'error', title: 'Gagal generate SKU' });
    } finally {
        btn.innerHTML = '<i class="fa-solid fa-bolt text-xs"></i> Auto';
        btn.disabled = false;
    }
}

// Preview barcode saat user mengetik SKU
document.getElementById('sku-input').addEventListener('input', function() {
    updateBarcodePreview(this.value);
});

function updateBarcodePreview(sku) {
    const box = document.getElementById('barcode-preview-box');
    if (!sku || sku.length < 8) {
        box.classList.add('hidden');
        return;
    }
    try {
        JsBarcode('#barcode-svg', sku, {
            format: 'CODE128',
            width: 2, height: 50,
            displayValue: true,
            fontSize: 12,
            margin: 8
        });
        box.classList.remove('hidden');
    } catch(e) {
        box.classList.add('hidden');
    }
}

// ===== FASE 3.2: Scan via Kamera HP/Webcam =====
function toggleCamera() {
    const container = document.getElementById('camera-container');
    if (container.classList.contains('hidden')) {
        container.classList.remove('hidden');
        startCamera();
    } else {
        stopCamera();
    }
}

function startCamera() {
    html5QrCode = new Html5Qrcode('reader');
    html5QrCode.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 250, height: 100 } },
        (decodedText) => {
            document.getElementById('sku-input').value = decodedText;
            updateBarcodePreview(decodedText);
            stopCamera();
            Toast.fire({ icon: 'success', title: 'Barcode berhasil discan: ' + decodedText });
        }
    ).catch(err => {
        console.error('Camera error:', err);
        Toast.fire({ icon: 'error', title: 'Tidak bisa mengakses kamera' });
    });
}

function stopCamera() {
    if (html5QrCode?.isScanning) {
        html5QrCode.stop().then(() => {
            document.getElementById('camera-container').classList.add('hidden');
        });
    } else {
        document.getElementById('camera-container').classList.add('hidden');
    }
}

// Preview foto produk
function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('foto-preview').src = e.target.result;
            document.getElementById('foto-preview').classList.remove('hidden');
            document.getElementById('foto-placeholder').classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Live kalkulator laba
function hitungLaba() {
    const hpp = parseFloat(document.querySelector('[name=modal_hpp]').value) || 0;
    const jual = parseFloat(document.querySelector('[name=harga_jual]').value) || 0;
    const laba = jual - hpp;
    const pct = hpp > 0 ? ((laba / hpp) * 100).toFixed(1) : 0;

    const box = document.getElementById('laba-info');
    if (jual > 0 || hpp > 0) {
        box.classList.remove('hidden');
        box.classList.add('flex');
        document.getElementById('laba-amount').textContent = 'Rp ' + laba.toLocaleString('id-ID');
        document.getElementById('laba-pct').textContent = '(' + pct + '%)';
        document.getElementById('laba-amount').className = 'font-bold ' + (laba >= 0 ? 'text-emerald-700' : 'text-red-600');
    } else {
        box.classList.add('hidden');
        box.classList.remove('flex');
    }
}

// Init preview jika ada old value
window.addEventListener('DOMContentLoaded', () => {
    const skuVal = document.getElementById('sku-input').value;
    if (skuVal) updateBarcodePreview(skuVal);
    hitungLaba();
});
</script>
@endpush
