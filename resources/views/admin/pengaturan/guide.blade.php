@extends('layouts.app')

@section('title', 'Petunjuk Penggunaan')
@section('page-title', 'Panduan Aplikasi POS')
@section('page-subtitle', 'Petunjuk lengkap cara menggunakan sistem Point of Sale')

@section('content')
<div class="flex flex-col lg:flex-row gap-6 items-start animate-fade-in">

    {{-- Sticky Table of Contents --}}
    <div class="w-full lg:w-1/4 bg-white rounded-2xl shadow-sm border border-slate-200 lg:sticky lg:top-6 shrink-0">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 rounded-t-2xl">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-list-ul text-blue-500"></i> Daftar Isi
            </h3>
        </div>
        <div class="p-2 space-y-1">
            <a href="#sec-pendahuluan" class="block px-4 py-2 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-600 rounded-xl transition-colors font-medium">1. Pendahuluan</a>
            <a href="#sec-master-data" class="block px-4 py-2 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-600 rounded-xl transition-colors font-medium">2. Master Data (Produk &amp; Kategori)</a>
            <a href="#sec-stok" class="block px-4 py-2 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-600 rounded-xl transition-colors font-medium">3. Manajemen Stok</a>
            <a href="#sec-kasir" class="block px-4 py-2 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-600 rounded-xl transition-colors font-medium">4. Transaksi Kasir (POS)</a>
            <a href="#sec-laporan" class="block px-4 py-2 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-600 rounded-xl transition-colors font-medium">5. Laporan Bisnis</a>
            <a href="#sec-pengaturan" class="block px-4 py-2 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-600 rounded-xl transition-colors font-medium">6. Pengaturan &amp; Backup</a>
        </div>
    </div>

    {{-- Content --}}
    <div class="flex-1 space-y-6 w-full">

        {{-- 1. Pendahuluan --}}
        <div id="sec-pendahuluan" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden scroll-mt-6">
            <div class="bg-slate-700 px-4 sm:px-6 py-2.5 sm:py-3" style="background: linear-gradient(to right, #334155, #1e293b)">
                <h2 class="text-white font-semibold text-sm sm:text-base flex items-center gap-2">
                    <i class="fa-solid fa-rocket text-blue-300 text-sm"></i> 1. Pendahuluan
                </h2>
            </div>
            <div class="p-4 sm:p-6 prose prose-sm prose-slate max-w-none">
                <p>Selamat datang di <strong>Aplikasi Point of Sale (POS)</strong>. Aplikasi ini dirancang untuk memudahkan Anda dalam mengelola penjualan, memantau stok barang, mengelola data produk, dan melihat laporan bisnis secara <em>real-time</em>.</p>
                <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl mt-4">
                    <h4 class="font-bold text-blue-800 mt-0 mb-2">Hak Akses Role</h4>
                    <ul class="text-blue-700 space-y-1 mb-0 pl-5">
                        <li><strong>Admin:</strong> Memiliki akses penuh ke seluruh fitur aplikasi termasuk Master Data, Manajemen Stok, Laporan, dan Pengaturan Sistem.</li>
                        <li><strong>Kasir:</strong> Hanya memiliki akses ke fitur Transaksi (POS) dan Riwayat Transaksi miliknya sendiri. Kasir tidak dapat memodifikasi data produk maupun pengaturan.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- 2. Master Data --}}
        <div id="sec-master-data" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden scroll-mt-6">
            <div class="bg-blue-600 px-4 sm:px-6 py-2.5 sm:py-3" style="background: linear-gradient(to right, #2563eb, #1d4ed8)">
                <h2 class="text-white font-semibold text-sm sm:text-base flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-blue-200 text-sm"></i> 2. Master Data (Produk, Kategori, Supplier)
                </h2>
            </div>
            <div class="p-4 sm:p-6 space-y-6">
                <div>
                    <h3 class="font-bold text-slate-800 text-base mb-2 border-b pb-2">A. Kategori &amp; Supplier</h3>
                    <p class="text-sm text-slate-600 mb-3">Sebelum menambahkan produk, pastikan Anda telah membuat <strong>Kategori</strong> dan <strong>Supplier</strong> (jika ada). Ini akan memudahkan Anda dalam mengelompokkan barang.</p>
                    <ul class="list-disc pl-5 text-sm text-slate-600 space-y-1">
                        <li>Navigasi ke menu <strong>Master Data &gt; Kategori</strong> untuk menambah kategori baru (Contoh: Makanan, Minuman, Elektronik).</li>
                        <li>Navigasi ke menu <strong>Master Data &gt; Supplier</strong> untuk mengelola data pemasok barang Anda.</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-base mb-2 border-b pb-2">B. Manajemen Produk</h3>
                    <p class="text-sm text-slate-600 mb-3">Produk adalah inti dari aplikasi ini. Setiap produk memiliki informasi harga beli, harga jual, dan stok.</p>
                    <ul class="list-disc pl-5 text-sm text-slate-600 space-y-2">
                        <li><strong>Tambah Produk:</strong> Masuk ke menu <strong>Produk</strong> dan klik "Tambah Produk Baru". Isi form dengan lengkap.</li>
                        <li><strong>SKU / Barcode:</strong> Anda bisa membiarkan sistem membuatkan kode SKU otomatis 12-digit dengan mengklik tombol <i class="fa-solid fa-wand-magic-sparkles text-amber-500 mx-1"></i> (Generate), atau Anda bisa <em>scan</em> barcode dari kemasan produk fisik langsung ke kolom SKU menggunakan <em>Barcode Scanner</em>.</li>
                        <li><strong>Margin Keuntungan:</strong> Sistem akan otomatis menghitung persentase margin keuntungan saat Anda memasukkan Harga Beli dan Harga Jual.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- 3. Manajemen Stok --}}
        <div id="sec-stok" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden scroll-mt-6">
            <div class="bg-emerald-600 px-4 sm:px-6 py-2.5 sm:py-3" style="background: linear-gradient(to right, #059669, #047857)">
                <h2 class="text-white font-semibold text-sm sm:text-base flex items-center gap-2">
                    <i class="fa-solid fa-cubes text-emerald-200 text-sm"></i> 3. Manajemen Stok
                </h2>
            </div>
            <div class="p-4 sm:p-6 space-y-4">
                <p class="text-sm text-slate-600">Aplikasi POS ini menggunakan sistem pencatatan stok yang ketat. Anda tidak dapat mengubah jumlah stok secara langsung di form edit produk demi menjaga keamanan dan validitas data.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div class="border border-emerald-200 bg-emerald-50 p-4 rounded-xl">
                        <h4 class="font-bold text-emerald-800 mb-2"><i class="fa-solid fa-download mr-1"></i> Stok Masuk</h4>
                        <p class="text-xs text-emerald-700">Digunakan ketika ada barang masuk dari supplier atau retur. Menambah jumlah stok saat ini.</p>
                        <p class="text-xs text-emerald-800 mt-2 font-medium">Menu: Manajemen Stok &gt; Stok Masuk</p>
                    </div>
                    <div class="border border-rose-200 bg-rose-50 p-4 rounded-xl">
                        <h4 class="font-bold text-rose-800 mb-2"><i class="fa-solid fa-upload mr-1"></i> Stok Keluar</h4>
                        <p class="text-xs text-rose-700">Digunakan untuk barang rusak, kedaluwarsa, atau hilang. Mengurangi jumlah stok saat ini.</p>
                        <p class="text-xs text-rose-800 mt-2 font-medium">Menu: Manajemen Stok &gt; Stok Keluar</p>
                    </div>
                </div>
                <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl mt-4 text-sm text-slate-600">
                    <i class="fa-solid fa-circle-info text-blue-500 mr-1"></i> <strong>Catatan:</strong> Stok juga akan berkurang secara otomatis ketika Kasir melakukan transaksi penjualan. Seluruh perubahan stok (Manual &amp; Penjualan) tercatat detail di menu <strong>Riwayat Mutasi</strong>.
                </div>
            </div>
        </div>

        {{-- 4. Transaksi Kasir --}}
        <div id="sec-kasir" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden scroll-mt-6">
            <div class="bg-violet-600 px-4 sm:px-6 py-2.5 sm:py-3" style="background: linear-gradient(to right, #7c3aed, #6d28d9)">
                <h2 class="text-white font-semibold text-sm sm:text-base flex items-center gap-2">
                    <i class="fa-solid fa-cash-register text-violet-200 text-sm"></i> 4. Transaksi Kasir (POS)
                </h2>
            </div>
            <div class="p-4 sm:p-6">
                <p class="text-sm text-slate-600 mb-4">Halaman Kasir (Point of Sale) didesain khusus agar transaksi dapat dilakukan dengan cepat menggunakan mouse, keyboard, maupun <em>barcode scanner</em>.</p>
                <ol class="list-decimal pl-5 text-sm text-slate-600 space-y-3">
                    <li><strong>Mencari Produk:</strong> Gunakan kolom pencarian di bagian atas atau klik langsung produk pada katalog gambar. Jika Anda menggunakan <em>Barcode Scanner</em>, cukup <em>scan</em> barang dan produk otomatis masuk ke keranjang.</li>
                    <li><strong>Mengubah Kuantitas:</strong> Di panel keranjang (sebelah kanan), klik tombol + atau - untuk menyesuaikan jumlah barang. Anda juga bisa mengetikkan angka secara langsung.</li>
                    <li><strong>Menghapus Item:</strong> Klik icon <i class="fa-solid fa-trash text-rose-500 mx-1"></i> pada item di keranjang untuk menghapusnya.</li>
                    <li><strong>Pembayaran:</strong> Klik tombol "Proses Pembayaran" (atau tekan F12/Enter). Masukkan jumlah uang tunai yang diterima (Uang Pelanggan). Sistem akan menampilkan jumlah uang kembalian.</li>
                    <li><strong>Cetak Struk:</strong> Setelah transaksi berhasil, Anda dapat mencetak struk dengan printer thermal 58mm. Jangan lupa mengizinkan <em>pop-up</em> di browser Anda agar struk bisa dicetak.</li>
                </ol>
            </div>
        </div>

        {{-- 5. Laporan Bisnis --}}
        <div id="sec-laporan" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden scroll-mt-6">
            <div class="bg-amber-500 px-4 sm:px-6 py-2.5 sm:py-3" style="background: linear-gradient(to right, #f59e0b, #d97706)">
                <h2 class="text-white font-semibold text-sm sm:text-base flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-amber-100 text-sm"></i> 5. Laporan Bisnis
                </h2>
            </div>
            <div class="p-4 sm:p-6 space-y-4">
                <p class="text-sm text-slate-600">Pantau performa bisnis Anda secara menyeluruh melalui menu <strong>Laporan</strong> (Hanya untuk Admin).</p>
                <ul class="list-disc pl-5 text-sm text-slate-600 space-y-2">
                    <li><strong>Laporan Penjualan:</strong> Melihat rekap transaksi dan pendapatan harian/bulanan. Anda dapat memfilter berdasarkan tanggal dan nama kasir. Laporan ini dapat diekspor ke file Excel.</li>
                    <li><strong>Laporan Stok:</strong> Melihat nilai aset stok Anda (berdasarkan harga beli) serta potensi pendapatan (berdasarkan harga jual). Membantu Anda merencanakan restock barang yang sudah menipis.</li>
                    <li><strong>Log Aktivitas:</strong> Memantau seluruh aktivitas yang dilakukan oleh user (misal: "Admin menghapus produk X", "Kasir Y melakukan login").</li>
                </ul>
            </div>
        </div>

        {{-- 6. Pengaturan --}}
        <div id="sec-pengaturan" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden scroll-mt-6">
            <div class="bg-slate-700 px-4 sm:px-6 py-2.5 sm:py-3" style="background: linear-gradient(to right, #334155, #1e293b)">
                <h2 class="text-white font-semibold text-sm sm:text-base flex items-center gap-2">
                    <i class="fa-solid fa-gears text-slate-300 text-sm"></i> 6. Pengaturan Sistem &amp; Backup
                </h2>
            </div>
            <div class="p-4 sm:p-6 space-y-4">
                <p class="text-sm text-slate-600">Menu Pengaturan digunakan untuk mengelola konfigurasi utama aplikasi.</p>
                <ul class="list-disc pl-5 text-sm text-slate-600 space-y-3">
                    <li><strong>Identitas Toko &amp; Struk:</strong> Anda dapat mengubah Nama Toko, Alamat, Logo (yang akan tampil di header struk), serta pesan penutup/footer pada struk.</li>
                    <li><strong>Pajak (PPN):</strong> Anda dapat mengatur persentase pajak default yang akan diterapkan otomatis ke total belanja pelanggan.</li>
                    <li><strong>Backup Database:</strong> Sangat disarankan untuk rutin melakukan backup database (mengunduh file <code>.sql</code>) setidaknya seminggu sekali untuk mencegah kehilangan data akibat kerusakan server/komputer.</li>
                    <li><strong>Import Produk via Excel:</strong> Jika Anda memiliki ribuan produk, Anda dapat menggunakan fitur ini. <em>Download Template</em> Excel yang disediakan, isi data produk, dan <em>Upload</em> kembali ke sistem untuk import massal.</li>
                </ul>
            </div>
        </div>

        <div class="text-center pb-8 pt-4">
            <p class="text-slate-500 text-sm">Aplikasi POS &copy; {{ date('Y') }}. Hak Cipta Dilindungi.</p>
        </div>

    </div>
</div>
@endsection
