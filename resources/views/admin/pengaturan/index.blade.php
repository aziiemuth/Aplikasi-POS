@extends('layouts.app')

@section('title', 'Pengaturan Toko')
@section('page-title', 'Pengaturan Sistem')
@section('page-subtitle', 'Kelola identitas toko, backup data, dan import produk')

@section('content')
<div class="space-y-6 animate-fade-in">

    {{-- ========================================================
         SECTION 8.1 — IDENTITAS TOKO & KUSTOMISASI STRUK
    ======================================================== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-blue-600 px-4 sm:px-6 py-2.5 sm:py-3 flex flex-row items-center gap-3" style="background: linear-gradient(to right, #2563eb, #1d4ed8)">
            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center shrink-0">
                <i class="fa-solid fa-store text-white text-sm"></i>
            </div>
            <div>
                <h2 class="text-white font-semibold text-sm sm:text-base leading-tight">Identitas Toko &amp; Kustomisasi Struk</h2>
                <p class="text-blue-100 text-[11px] sm:text-xs">Nama toko, alamat, logo, dan pesan footer struk</p>
            </div>
        </div>

        <form action="{{ route('admin.pengaturan.identitas') }}" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Nama Toko --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        <i class="fa-solid fa-store text-blue-500 mr-1"></i> Nama Toko <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_toko" id="nama_toko"
                        value="{{ old('nama_toko', $settings['nama_toko'] ?? 'Aplikasi POS') }}"
                        class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                        placeholder="Nama toko Anda..." required>
                    @error('nama_toko')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Kontak --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        <i class="fa-solid fa-phone text-blue-500 mr-1"></i> Nomor Kontak / Telepon
                    </label>
                    <input type="text" name="kontak" id="kontak"
                        value="{{ old('kontak', $settings['kontak'] ?? '') }}"
                        class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                        placeholder="0812-3456-7890">
                </div>

                {{-- Alamat --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        <i class="fa-solid fa-location-dot text-blue-500 mr-1"></i> Alamat Toko
                    </label>
                    <input type="text" name="alamat" id="alamat"
                        value="{{ old('alamat', $settings['alamat'] ?? '') }}"
                        class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                        placeholder="Jl. Contoh No. 1, RT 01/RW 01">
                </div>

                {{-- Kota --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        <i class="fa-solid fa-city text-blue-500 mr-1"></i> Kota
                    </label>
                    <input type="text" name="kota" id="kota"
                        value="{{ old('kota', $settings['kota'] ?? '') }}"
                        class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                        placeholder="Jakarta">
                </div>

                {{-- Website --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        <i class="fa-solid fa-globe text-blue-500 mr-1"></i> Website / Instagram <span class="text-slate-400 font-normal">(opsional)</span>
                    </label>
                    <input type="text" name="website" id="website"
                        value="{{ old('website', $settings['website'] ?? '') }}"
                        class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                        placeholder="www.tokoku.com / @tokoku">
                </div>

                {{-- Pajak Default --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        <i class="fa-solid fa-percent text-blue-500 mr-1"></i> Pajak/PPN Default (%)
                    </label>
                    <input type="number" name="pajak_default" id="pajak_default" min="0" max="100" step="0.1"
                        value="{{ old('pajak_default', $settings['pajak_default'] ?? 0) }}"
                        class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                        placeholder="0">
                    <p class="text-[10px] text-slate-400 mt-1">Isi 0 jika tidak ada pajak</p>
                </div>

                {{-- Footer Struk --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        <i class="fa-solid fa-receipt text-blue-500 mr-1"></i> Pesan Footer Struk
                    </label>
                    <input type="text" name="footer_struk" id="footer_struk"
                        value="{{ old('footer_struk', $settings['footer_struk'] ?? 'Terima kasih sudah berbelanja!') }}"
                        class="w-full border border-slate-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                        placeholder="Terima kasih sudah berbelanja!">
                </div>

                {{-- Upload Logo --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                        <i class="fa-solid fa-image text-blue-500 mr-1"></i> Logo Toko
                        <span class="text-slate-400 font-normal">(PNG/JPG, maks 2MB — tampil di header struk)</span>
                    </label>

                    <div class="flex flex-col sm:flex-row items-start gap-4">
                        {{-- Preview logo saat ini --}}
                        @if(!empty($settings['logo']))
                            <div class="flex flex-col items-center gap-2 shrink-0">
                                <div class="w-20 h-20 rounded-xl border-2 border-blue-200 bg-slate-50 flex items-center justify-center overflow-hidden">
                                    <img src="{{ Storage::url($settings['logo']) }}" alt="Logo Toko" class="max-w-full max-h-full object-contain">
                                </div>
                                <button type="button" onclick="if(confirm('Hapus logo?')) document.getElementById('form-delete-logo').submit();" class="text-xs text-rose-500 hover:underline flex items-center gap-1">
                                    <i class="fa-solid fa-trash text-xs"></i> Hapus Logo
                                </button>
                            </div>
                        @else
                            <div class="w-20 h-20 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-image text-slate-300 text-2xl"></i>
                            </div>
                        @endif

                        <div class="flex-1">
                            <label for="logo"
                                class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer bg-slate-50 hover:bg-blue-50 hover:border-blue-300 transition-all group">
                                <i class="fa-solid fa-cloud-arrow-up text-xl text-slate-400 group-hover:text-blue-500 transition-colors mb-1"></i>
                                <span class="text-xs text-slate-500 group-hover:text-blue-600">Klik untuk upload logo baru</span>
                                <span class="text-[10px] text-slate-400 mt-0.5">PNG, JPG, WEBP (maks 2MB)</span>
                                <input id="logo" name="logo" type="file" accept="image/*" class="hidden"
                                    onchange="previewLogo(this)">
                            </label>
                            <div id="logo-preview-new" class="mt-2 hidden">
                                <p class="text-xs text-slate-500"><i class="fa-solid fa-check-circle text-emerald-500"></i> File dipilih: <span id="logo-filename"></span></p>
                            </div>
                        </div>
                    </div>
                    @error('logo')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

            </div>{{-- end grid --}}

            <div class="mt-4 flex flex-col sm:flex-row sm:justify-end">
                <button type="submit"
                    class="w-full sm:w-auto flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-xl font-semibold text-sm transition-all shadow hover:-translate-y-0.5">
                    <i class="fa-solid fa-floppy-disk"></i>
                    Simpan Pengaturan Toko
                </button>
            </div>
        </form>

        {{-- Form terpisah untuk hapus logo agar tidak nested form --}}
        <form id="form-delete-logo" action="{{ route('admin.pengaturan.logo.delete') }}" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>
    </div>

    {{-- ========================================================
         SECTION 8.2 — BACKUP DATABASE & IMPORT EXCEL
    ======================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- BACKUP --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-emerald-600 px-4 sm:px-6 py-2.5 sm:py-3 flex items-center gap-3" style="background: linear-gradient(to right, #059669, #047857)">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-database text-white text-sm"></i>
                </div>
                <div>
                    <h2 class="text-white font-semibold text-sm sm:text-base leading-tight">Backup Database</h2>
                    <p class="text-emerald-100 text-[11px] sm:text-xs">Unduh cadangan seluruh data sistem</p>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-5">
                    <div class="flex gap-3">
                        <i class="fa-solid fa-circle-info text-emerald-600 mt-0.5 shrink-0"></i>
                        <div class="text-xs text-emerald-800">
                            <p class="font-semibold mb-1">Apa yang dibackup?</p>
                            <ul class="list-disc list-inside space-y-0.5 text-emerald-700">
                                <li>Seluruh data produk, kategori, supplier</li>
                                <li>Semua riwayat transaksi &amp; order items</li>
                                <li>Data user &amp; log aktivitas</li>
                                <li>Riwayat mutasi stok</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-xl border border-slate-200 mb-4">
                    <div>
                        <p class="text-xs font-semibold text-slate-700">Backup Terakhir</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Belum ada info backup tersimpan</p>
                    </div>
                    <i class="fa-solid fa-clock text-slate-300 text-xl"></i>
                </div>

                <a href="{{ route('admin.pengaturan.backup') }}" id="btn-backup"
                    onclick="runBackup(this)"
                    class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-xl font-semibold text-sm transition-all shadow hover:-translate-y-0.5">
                    <i class="fa-solid fa-download"></i>
                    Download Backup Sekarang (.sql)
                </a>
            </div>
        </div>

        {{-- IMPORT EXCEL --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-violet-600 px-4 sm:px-6 py-2.5 sm:py-3 flex items-center gap-3" style="background: linear-gradient(to right, #7c3aed, #6d28d9)">
                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-file-excel text-white text-sm"></i>
                </div>
                <div>
                    <h2 class="text-white font-semibold text-sm sm:text-base leading-tight">Import Produk via Excel</h2>
                    <p class="text-violet-100 text-[11px] sm:text-xs">Tambah 1000+ produk sekaligus dari file .xlsx</p>
                </div>
            </div>

            <div class="p-4 sm:p-6">
                <div class="bg-violet-50 border border-violet-200 rounded-xl p-4 mb-5">
                    <div class="flex gap-3">
                        <i class="fa-solid fa-lightbulb text-violet-600 mt-0.5 shrink-0"></i>
                        <div class="text-xs text-violet-800">
                            <p class="font-semibold mb-1">Cara Penggunaan:</p>
                            <ol class="list-decimal list-inside space-y-0.5 text-violet-700">
                                <li>Download template Excel di bawah</li>
                                <li>Isi data produk sesuai format template</li>
                                <li>Upload kembali file yang sudah diisi</li>
                                <li>Sistem akan import otomatis</li>
                            </ol>
                        </div>
                    </div>
                </div>

                {{-- Download Template --}}
                <a href="{{ route('admin.pengaturan.import.template') }}"
                    class="w-full flex items-center justify-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl font-semibold text-sm transition-all border border-slate-200 mb-4">
                    <i class="fa-solid fa-file-arrow-down text-emerald-600"></i>
                    Download Template Excel
                </a>

                {{-- Upload Form --}}
                <form action="{{ route('admin.pengaturan.import.produk') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label for="file_excel"
                        class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer bg-slate-50 hover:bg-violet-50 hover:border-violet-300 transition-all group mb-3">
                        <i class="fa-solid fa-file-excel text-xl text-slate-400 group-hover:text-violet-500 transition-colors mb-1"></i>
                        <span class="text-xs text-slate-500 group-hover:text-violet-600">Pilih file Excel (.xlsx / .xls)</span>
                        <span id="excel-filename" class="text-[10px] text-slate-400 mt-0.5">Belum ada file dipilih</span>
                        <input id="file_excel" name="file_excel" type="file" accept=".xlsx,.xls,.csv" class="hidden"
                            onchange="document.getElementById('excel-filename').textContent = (this.files && this.files[0]) ? this.files[0].name : 'Belum ada file dipilih'">
                    </label>
                    @error('file_excel')<p class="text-rose-500 text-xs mb-2">{{ $message }}</p>@enderror

                    <button type="submit"
                        class="w-full flex items-center justify-center gap-2 bg-violet-600 hover:bg-violet-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition-all shadow hover:-translate-y-0.5">
                        <i class="fa-solid fa-upload"></i>
                        Mulai Import Produk
                    </button>
                </form>
            </div>
        </div>

    </div>{{-- end grid backup & import --}}

    {{-- ========================================================
         SECTION 8.4 — ZONA BAHAYA / DANGER ZONE
    ======================================================== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-rose-200 overflow-hidden">
        <div class="bg-rose-600 px-4 sm:px-6 py-2.5 sm:py-3 flex items-center gap-3" style="background: linear-gradient(to right, #e11d48, #be123c)">
            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center shrink-0">
                <i class="fa-solid fa-triangle-exclamation text-white text-sm"></i>
            </div>
            <div>
                <h2 class="text-white font-semibold text-sm sm:text-base leading-tight">Zona Bahaya (Testing)</h2>
                <p class="text-rose-100 text-[11px] sm:text-xs">Tindakan ini tidak dapat dibatalkan</p>
            </div>
        </div>

        <div class="p-4 sm:p-6">
            <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 mb-4">
                <div class="flex gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 mt-0.5 shrink-0"></i>
                    <div class="text-xs text-rose-800 space-y-1">
                        <p class="font-bold">⚠️ PENTING: Apa yang terjadi saat database disetel ulang?</p>
                        <p>Seluruh data transaksi, item penjualan, mutasi stok, keranjang kasir, data produk, kategori, supplier, dan log aktivitas akan **dihapus bersih (kosong)**.</p>
                        <p class="font-semibold text-rose-700">Data Pengguna (User Accounts) tidak akan dihapus, sehingga Anda tetap bisa login kembali dengan akun yang sama.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-slate-50 border border-slate-200 rounded-xl">
                <div class="text-center sm:text-left">
                    <p class="text-xs font-bold text-slate-700">Setel Ulang Seluruh Data Sistem</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Disarankan mengunduh backup database terlebih dahulu sebelum melakukan tindakan ini.</p>
                </div>
                <form id="form-reset-database" action="{{ route('admin.pengaturan.reset') }}" method="POST" class="w-full sm:w-auto">
                    @csrf
                    <button type="button" onclick="confirmResetDatabase()"
                        class="w-full sm:w-auto flex items-center justify-center gap-2 bg-rose-600 hover:bg-rose-700 text-white px-5 py-2.5 rounded-xl font-bold text-xs transition-all shadow hover:-translate-y-0.5">
                        <i class="fa-solid fa-trash-can"></i>
                        Kosongkan Database
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ========================================================
         SECTION 8.3 — REQUEST FEATURE / BANTUAN TEKNIS
    ======================================================== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="bg-slate-700 px-4 sm:px-6 py-2.5 sm:py-3 flex items-center gap-3" style="background: linear-gradient(to right, #334155, #1e293b)">
            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center shrink-0">
                <i class="fa-solid fa-headset text-white text-sm"></i>
            </div>
            <div>
                <h2 class="text-white font-semibold text-sm sm:text-base leading-tight">Bantuan &amp; Request Fitur</h2>
                <p class="text-slate-300 text-[11px] sm:text-xs">Hubungi developer langsung via WhatsApp</p>
            </div>
        </div>

        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Request Feature --}}
                <a href="https://wa.me/62816234185?text={{ urlencode('Halo, saya pengguna Aplikasi POS dan saya ingin request fitur tambahan. Nama toko: ' . ($settings['nama_toko'] ?? 'Aplikasi POS') . '. Fitur yang diinginkan: ') }}"
                    target="_blank"
                    class="group flex flex-col items-center gap-3 p-5 bg-emerald-50 border border-emerald-200 rounded-2xl hover:bg-emerald-100 hover:border-emerald-300 hover:shadow-md transition-all hover:-translate-y-0.5">
                    <div class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform">
                        <i class="fa-brands fa-whatsapp text-white text-3xl"></i>
                    </div>
                    <div class="text-center">
                        <p class="font-bold text-emerald-800 text-sm">Request Fitur Baru</p>
                        <p class="text-emerald-600 text-xs mt-1">Minta penambahan fitur sesuai kebutuhan toko</p>
                    </div>
                </a>

                {{-- Laporan Bug --}}
                <a href="https://wa.me/62816234185?text={{ urlencode('Halo, saya pengguna Aplikasi POS dan ingin melaporkan bug/masalah. Nama toko: ' . ($settings['nama_toko'] ?? 'Aplikasi POS') . '. Masalah yang terjadi: ') }}"
                    target="_blank"
                    class="group flex flex-col items-center gap-3 p-5 bg-rose-50 border border-rose-200 rounded-2xl hover:bg-rose-100 hover:border-rose-300 hover:shadow-md transition-all hover:-translate-y-0.5">
                    <div class="w-14 h-14 bg-rose-500 rounded-2xl flex items-center justify-center shadow-lg shadow-rose-200 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-bug text-white text-2xl"></i>
                    </div>
                    <div class="text-center">
                        <p class="font-bold text-rose-800 text-sm">Laporan Bug / Error</p>
                        <p class="text-rose-600 text-xs mt-1">Laporkan kesalahan atau gangguan sistem</p>
                    </div>
                </a>

                {{-- Bantuan Teknis --}}
                <a href="https://wa.me/62816234185?text={{ urlencode('Halo, saya pengguna Aplikasi POS dan membutuhkan bantuan teknis. Nama toko: ' . ($settings['nama_toko'] ?? 'Aplikasi POS') . '. Pertanyaan: ') }}"
                    target="_blank"
                    class="group flex flex-col items-center gap-3 p-5 bg-blue-50 border border-blue-200 rounded-2xl hover:bg-blue-100 hover:border-blue-300 hover:shadow-md transition-all hover:-translate-y-0.5">
                    <div class="w-14 h-14 bg-blue-500 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-life-ring text-white text-2xl"></i>
                    </div>
                    <div class="text-center">
                        <p class="font-bold text-blue-800 text-sm">Bantuan Teknis</p>
                        <p class="text-blue-600 text-xs mt-1">Tanyakan cara penggunaan aplikasi</p>
                    </div>
                </a>

            </div>

            <div class="mt-4 flex flex-col sm:flex-row items-center gap-2 sm:gap-4 text-center justify-center text-sm text-slate-500">
                <span><i class="fa-solid fa-phone-volume text-slate-400 mr-1"></i> Developer: <strong class="text-slate-700">0816-234-185</strong></span>
                <span class="hidden sm:inline">·</span>
                <span><i class="fa-solid fa-clock text-slate-400 mr-1"></i> Waktu respon: Senin–Sabtu, 09.00–21.00 WIB</span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function previewLogo(input) {
    const preview = document.getElementById('logo-preview-new');
    const filename = document.getElementById('logo-filename');
    if (input.files && input.files[0]) {
        preview.classList.remove('hidden');
        filename.textContent = input.files[0].name;
    }
}

function runBackup(btn) {
    const oldHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-2"></i>Sedang memproses...';
    btn.classList.add('opacity-75', 'pointer-events-none');
    setTimeout(() => {
        btn.innerHTML = oldHtml;
        btn.classList.remove('opacity-75', 'pointer-events-none');
    }, 3000);
}

function confirmResetDatabase() {
    Swal.fire({
        title: 'Konfirmasi Reset Database',
        html: '<div class="text-slate-600 text-sm">Apakah Anda yakin ingin <strong>menghapus bersih seluruh database</strong>?<br><br><span class="text-rose-500 font-bold text-sm">⚠️ TINDAKAN INI TIDAK DAPAT DIBATALKAN.</span><br><small class="text-slate-400 mt-2 block">Seluruh data produk, kategori, stok, supplier, log, dan riwayat transaksi akan dihapus secara permanen (Kecuali akun User/Kasir).</small></div>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Ya, Reset Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        width: '440px',
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-xl px-5 py-2 text-sm font-semibold',
            cancelButton: 'rounded-xl px-5 py-2 text-sm font-semibold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Verifikasi Terakhir',
                text: 'Ketik "RESET" pada kolom di bawah untuk memverifikasi tindakan Anda:',
                input: 'text',
                inputPlaceholder: 'RESET',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Proses Reset',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                width: '420px',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-5 py-2 text-sm font-semibold',
                    cancelButton: 'rounded-xl px-5 py-2 text-sm font-semibold'
                },
                inputValidator: (value) => {
                    if (value !== 'RESET') {
                        return 'Anda harus mengetik "RESET" dengan huruf besar!';
                    }
                }
            }).then((secondResult) => {
                if (secondResult.isConfirmed && secondResult.value === 'RESET') {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menyetel ulang database Anda...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    document.getElementById('form-reset-database').submit();
                }
            });
        }
    });
}
</script>
@endpush
