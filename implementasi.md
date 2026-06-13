# Alur Implementasi Project Aplikasi POS (Point of Sale)

Dokumen ini berisi panduan dan alur kerja (workflow) langkah demi langkah untuk membangun Aplikasi POS sesuai dengan fitur dasar, fitur lanjutan (advanced), dan **sistem pembatasan hak akses yang ketat**.

---

## Fase 0: Panduan UI/UX & Desain Visual

Sebelum masuk ke tahap pengembangan fungsionalitas, berikut adalah standar desain antarmuka yang wajib dipatuhi:

1. **Palet Warna (Color Palette)**
    - Menggunakan warna dasar **Putih Soft bergradasi** (bukan putih murni yang mencolok/terang) sebagai latar belakang (_background_). Tujuannya agar antarmuka terlihat elegan, sejuk, dan tidak membuat mata kasir sakit/lelah saat menatap layar seharian.
2. **Tipografi & Keterbacaan (Solid Text)**
    - Seluruh teks harus memiliki kontras yang kuat dan warna yang **solid** (jelas, tegas, tidak samar-samar/terlalu terang). Pastikan tidak ada teks yang sulit dibaca di atas _background_ putih soft tersebut.
3. **Interaksi CRUD & Notifikasi (SweetAlert)**
    - Menggunakan modul **SweetAlert** yang telah dikustomisasi untuk setiap konfirmasi aksi (Tambah, Simpan, Edit, Hapus, Peringatan/Error).
    - Ukuran _popup_ SweetAlert harus di-_styling_ sedemikian rupa agar proporsional (tidak kebesaran/menutupi layar secara berlebihan), terlihat menarik, dan animasinya mulus (_smooth_).
4. **Kerangka UI Modern (Tailwind CSS) & User-Friendly**
    - Menggunakan utilitas **Tailwind CSS** untuk memberikan polesan desain antarmuka (_styling_) yang sangat modern, rapi, responsif, dan menawan.
    - **User-Friendly & Anti-Bosan**: Antarmuka harus dirancang seintuitif mungkin agar mudah dipahami (_user-friendly_). Tambahkan _micro-animations_ (seperti efek _hover_, transisi halus, dan umpan balik visual saat tombol ditekan) agar tampilan terasa hidup, interaktif, dan tidak membuat staf merasa bosan saat menggunakannya seharian penuh.
5. **Ikonografi Visual (FontAwesome)**
    - Integrasi **FontAwesome** untuk menyediakan ikon-ikon visual yang menarik pada setiap tombol, menu navigasi, dasbor, dan elemen antarmuka lainnya. Penggunaan ikon akan mempercepat pemahaman fungsi dan membuat aplikasi terlihat jauh lebih profesional.

---

## Fase 1: Setup Proyek & Desain Database (Pondasi)

Fase ini berfokus pada persiapan awal proyek dan pembuatan struktur database yang mencakup kebutuhan inventaris dan transaksi pintar.

1. **Inisialisasi Proyek & Teknologi (Laravel 12 + Livewire + Laravel Reverb)**
    - Setup _framework_ utama menggunakan **Laravel 12**.
    - Integrasi **Livewire** ke dalam _project_ agar antarmuka Kasir (POS) sangat interaktif tanpa _reload_.
    - Mengaktifkan **Laravel Reverb (WebSockets)**. Ini adalah teknologi inti yang menjadikan aplikasi benar-benar "Real-Time".
    - _(Catatan: Inertia.js tidak digunakan agar tidak terjadi bentrok arsitektur dengan Livewire. Kombinasi Livewire + Reverb adalah solusi paling stabil & ringan untuk POS)._

2. **Perancangan Database (Migrations & Integritas)**
    - Wajib mengimplementasikan **Soft Deletes** pada tabel `products` dan `users`. Jika produk/kasir dihapus oleh Admin, datanya tidak terhapus permanen dari _database_, melainkan hanya disembunyikan. Ini sangat krusial agar riwayat struk/laporan transaksi lama tidak _error_ atau hilang.
    - Buat file migration dengan skema **Relasi Antar Tabel (Foreign Keys)** yang ketat (*Referential Integrity*) untuk tabel-tabel berikut:
        - `users`: id, nama, username, password, role/hak_akses, timestamps, deleted_at.
        - `categories` (Kategori Produk): id, nama_kategori, deskripsi, timestamps.
        - `suppliers` (Data Pemasok): id, nama_supplier, kontak, alamat, timestamps.
        - `products` (Master Data): id, category_id (FK), sku/barcode, satuan, nama, deskripsi, modal_hpp, harga_jual, stok_saat_ini, foto, timestamps, deleted_at.
        - `stock_mutations` (Kartu Stok): id, product_id (FK), user_id (FK - Siapa yg merubah), order_id (FK - Opsional, referensi jika stok keluar karena terjual), tipe (masuk/keluar), jumlah, harga_beli, supplier_id (FK - Opsional), timestamps.
        - `orders` (Transaksi): id, user_id (FK - Kasir yg melayani), nomor_order, nama_customer, total_sebelum_diskon, diskon_global, pajak_ppn, total_pembayaran, metode_pembayaran, jumlah_bayar, kembalian, status (lunas/open_bill), timestamps.
        - `order_items` (Detail Pesanan): id, order_id (FK), product_id (FK), harga_jual_snapshot, hpp_snapshot, diskon_item, jumlah, total_harga_item, timestamps.
        - `carts` (Keranjang Sementara): id, user_id, product_id, jumlah, diskon_item, timestamps.
        - `activity_logs` (Rekam Jejak Aktivitas): id, user_id, aksi, deskripsi, ip_address, timestamps.
        - `failed_jobs`: Untuk log sistem (biasanya bawaan framework).

3. **Seeding Database Dasar**
    - Buat seeder untuk akun admin pertama dan beberapa kategori *dummy*.

4. **Sistem Caching & Optimasi Performa (Anti-Lemot)**
    - Menerapkan **Query Caching** (penyimpanan memori sementara) pada tingkat _database_ untuk data yang sering diakses namun jarang berubah (misalnya: Daftar Kategori Produk dan Katalog Produk di halaman POS).
    - Mekanisme **Cache Invalidation**: *Cache* akan otomatis dibersihkan (*refresh*) hanya ketika Admin menambah/mengedit/menghapus barang. Ini akan mengurangi beban _server_ (_server load_) secara drastis hingga 80%, memastikan aplikasi tetap super responsif meskipun menangani ribuan transaksi harian dari banyak Kasir secara bersamaan.
---

## Fase 2: Manajemen User & Keamanan Akses (RBAC Ketat)

Fase ini berfokus pada keamanan dan **pembagian hak akses yang sangat ketat (Strict Role-Based Access Control)** untuk mencegah kecurangan.

1. **Pembatasan Hak Akses & Antarmuka Dinamis**
    - **Tampilan Menyesuaikan Role**: Setelah login berhasil, navigasi, _sidebar_, dan seluruh fitur akan otomatis menyesuaikan dengan perannya (_Dynamic UI_). Menu yang bukan haknya akan otomatis disembunyikan dan diproteksi aksesnya.
    - **Role Kasir**: **HANYA** memiliki akses ke layar POS (Point of Sale) untuk melakukan penjualan kepada _customer_. Kasir **TIDAK BISA** mengubah stok secara manual, melihat laporan stok, atau melihat data _supplier_.
    - **Role Admin / Owner**: Memiliki akses penuh (Superuser) ke seluruh sistem termasuk Master Data, Manajemen Stok, Data _Supplier_, dan semua jenis Laporan.

2. **Manajemen User (Admin Only)**
    - Daftar user, tambah, edit, hapus, dan atur _password_ staf.

3. **Keamanan Sistem Dasar (Anti Serangan Luar)**
    - **Proteksi Brute-Force (Rate Limiting)**: Membatasi jumlah percobaan _login_ yang salah (misalnya maksimal 5 kali). Jika gagal berturut-turut, sistem akan memblokir akses dari IP tersebut sementara waktu untuk mencegah _hacker_ menebak _password_.
    - **Anti SQL-Injection & XSS**: Menggunakan _query builder/Eloquent_ bawaan Laravel secara ketat dan sanitasi _input_ di seluruh _form_ untuk memastikan tidak ada kode berbahaya yang bisa disusupkan ke _database_ atau dieksekusi di layar.
    - **Proteksi CSRF**: Setiap _form_ dan aksi sensitif (seperti mengubah stok, menghapus barang, atau mencetak transaksi) wajib dibentengi oleh token CSRF untuk menangkal serangan sabotase dari aplikasi/website luar.
    - **Session Security**: Sistem akan memaksa _logout_ otomatis jika komputer kasir dibiarkan menyala tanpa aktivitas (_idle_) dalam durasi waktu tertentu guna mengamankan sesi kerja yang terbuka.

---

## Fase 3: Manajemen Inventaris & Produk (Khusus Admin/Owner)

Fase ini mengelola master data produk beserta alur stok yang sepenuhnya dikontrol oleh Admin untuk keamanan.

1. **Master Data & Supplier Lengkap**
    - Kelola kategori, satuan barang, modal (HPP), harga jual, gambar, dan kode SKU. Data sensitif seperti _Supplier_ (pemasok) sepenuhnya disembunyikan dari Kasir.

2. **Pembuatan Barcode Otomatis**
    - Fitur Generate Barcode unik 12-digit atau penyalinan SKU barang langsung menjadi barcode dengan 1x klik.
    - Scan untuk Barcode yg udah ada dibarang (pake alat scanner atau kamera hp(bila diakses via mobile))

3. **Stok Masuk & Keluar yang Ketat**
    - Penambahan/pengurangan stok hanya bisa dilakukan oleh Admin via _Stock Mutation_.
    - Tidak ada celah bagi Kasir untuk mengubah jumlah stok secara paksa tanpa melalui transaksi penjualan.

4. **Perhitungan HPP Rata-rata Tertimbang & Otomatisasi Laba**
    - Harga Pokok Penjualan (HPP/Modal Dasar) akan dihitung ulang secara otomatis menggunakan metode rata-rata tertimbang setiap kali ada "Stok Masuk" dengan harga beli yang berbeda.
    - **Pembaruan Otomatis**: Ketika terjadi perubahan nilai HPP dari kalkulasi ini, data modal (HPP) pada Master Produk akan langsung terbarui secara otomatis. Seluruh perhitungan laba untuk transaksi penjualan selanjutnya akan otomatis mengikuti nilai HPP terbaru tersebut secara _real-time_.

---

## Fase 4: Transaksi Kasir Pintar (POS) - Khusus Kasir & Admin

Fase ini merupakan ruang kerja utama Kasir untuk melakukan _checkout_.

1. **Antarmuka Responsif & Multi-Layout (Desktop & Mobile Modern)**
    - Desain UI/UX dibangun menggunakan pendekatan **Responsive Web Design modern**, sehingga tampilan akan otomatis menyesuaikan dengan sempurna dan proporsional baik di **Desktop (PC/Laptop)** maupun **Mobile (Smartphone/Tablet)**.
    - Fitur _layout_ dinamis: _Split screen_ untuk tablet/landscape, dan tata letak _touch-friendly_ yang nyaman untuk layar HP.
    - **Sistem Multi-Kasir**: Mendukung banyak akun kasir. Setiap transaksi akan melacak secara akurat kasir mana yang memproses pesanan tersebut (berguna untuk pelacakan _shift_ dan performa).

2. **Katalog Produk & Filter Kategori Dinamis**
    - Di antarmuka POS, terdapat fitur **Tab/Filter Kategori Produk** (data kategorinya di-_setting_ sebelumnya oleh Admin/Owner di Master Data).
    - Kasir tidak perlu repot men-_scroll_ panjang ke bawah atau mengetik nama satu per satu. Cukup dengan mengklik tombol kategori (misal: "Minuman Dingin" atau "Makanan Ringan"), daftar barang di layar langsung terfilter sesuai kelompoknya secara instan.

3. **Keranjang Belanja & Fitur Kasir Tingkat Lanjut**
    - Perhitungan subtotal per item dan kalkulator kembalian otomatis.
    - **Riwayat Open Bill (Hold Transaction)**: Jika pelanggan belum siap bayar (misal dompet tertinggal), Kasir bisa menekan tombol "Hold/Open Bill". Sistem akan memindahkan data dari Keranjang Sementara ke tabel `orders` dengan status `open_bill`, sehingga Kasir bisa melayani pelanggan antrean berikutnya tanpa kehilangan data transaksi sebelumnya.
    - **Larangan Stok Minus (Negative Stock)**: Sistem akan memberikan *alert* merah peringatan langsung di layar POS apabila barang yang di-scan stoknya 0 (kosong). Kasir **DILARANG KERAS** dan diblokir sistem untuk menjual barang dengan stok kosong demi menjaga agar rumus Akuntansi (HPP) tidak hancur/minus.

4. **Diskon Ganda & Pembayaran Fleksibel**
    - Diskon spesifik per-item dan diskon nominal global.
    - Kustomisasi metode pembayaran mandiri (Tunai, Debit BCA, OVO, QRIS, dll).

5. **Alur Logika Checkout & Pengamanan Perhitungan (Krusial)**
    - **Snapshot Data**: Saat transaksi sukses, sistem WAJIB mengkopi _Harga Jual_ dan _HPP_ saat itu ke tabel `order_items`. Ini mengamankan perhitungan Laba agar tidak berantakan/berubah meskipun harga master produk diubah di masa depan.
    - **Auto-Deduct Stock**: Sistem otomatis memotong `stok_saat_ini` pada tabel `products` dan otomatis membuat _log_ "Stok Keluar" di tabel `stock_mutations` (menjamin tidak ada stok keluar yang tidak terlacak).
    - Rumus Laba Otomatis: `(Harga Jual Snapshot - Diskon Item - HPP Snapshot) * Jumlah`. Laba akhir akan dikurangi dengan `Diskon Global` dan disesuaikan dengan komponen `Pajak/PPN` (jika fitur pajak diaktifkan). Logika akuntansi ini dijamin 100% akurat dan _bulletproof_.
    - **Sinkronisasi Stok Real-Time (Anti-Overselling)**: Berkat _Laravel Reverb_, ketika Kasir A berhasil menekan tombol "Bayar" dan stok berkurang, sistem akan memancarkan _event_ ke seluruh jaringan. Detik itu juga, layar perangkat Kasir B dan Kasir C akan berkedip/otomatis memperbarui angka sisa stok produk tersebut tanpa perlu _refresh_ halaman. Ini mencegah terjadinya barang minus karena dibeli bersamaan.

6. **Halaman Diagnostik & Testing Hardware (Kasir)**
    - Sebelum mulai berjualan, kasir dibekali menu khusus **"Uji Coba Alat"** langsung di layar POS mereka.
    - **Test Scanner**: Fitur mencoba _scan barcode_ acak (via Kamera/USB/Bluetooth) untuk memastikan data masuk dengan instan dan tanpa _double-scan_, serta memverifikasi bunyi _Beep_.
    - **Test Printer Thermal**: Tombol khusus untuk memancarkan perintah cetak _struk dummy_ (percobaan) guna memastikan koneksi printer USB/Bluetooth berhasil dan format struk 58mm sudah presisi (_margin_ pas) sebelum dihadapkan pada pelanggan asli.

---

## Fase 5: Pemindaian Barcode Tingkat Lanjut

Fase ini difokuskan pada kecepatan input barang saat transaksi di meja kasir.

1. **Dukungan Scanner Fisik (USB & Bluetooth)**
    - Sistem "Global Scanner" (standby instan tanpa klik kolom pencarian).

2. **Scanner Kamera HP/Webcam**
    - Dilengkapi fitur _Continuous Scan_ dengan jeda (delay) cerdas. 3 detik delaynya

3. **Suara Scanner Otomatis**
    - Fitur interaktif bunyi _Beep_ (file _audio_ khusus) setiap kali barang berhasil dipindai dan masuk keranjang.

---

## Fase 6: Pencetakan Struk Terintegrasi

Fase ini menangani _output_ transaksi kepada pelanggan.

1. **Struk Digital & Cetak Bluetooth/USB**
    - Download struk PNG, Share via WhatsApp.
    - Web Bluetooth API untuk Thermal Printer nirkabel.
    - HTML/CSS Layout Engine 58mm khusus untuk Thermal Printer USB (PC/Laptop).

---

## Fase 7: Laporan Bisnis Komprehensif (Khusus Admin/Owner)

Fase terakhir menyajikan alat pelaporan yang detail, terstruktur, dan siap di-export.

1. **Dashboard Ringkasan & Tren Penjualan**
    - Cek Omzet, Laba Bersih, Laba Kotor (dipotong diskon), peringatan stok menipis, dan grafik **Tren Penjualan**.

2. **Laporan Penjualan & Laporan Stok Berbasis Filter**
    - Sistem pelaporan mendukung penyajian data **Harian, Bulanan, dan Tahunan**.
    - **Custom Filter Waktu**: Admin dapat memilih _range_ tanggal spesifik, bulan, atau tahun tertentu untuk melihat riwayat penjualan dan riwayat stok masuk/keluar (Mutasi Stok).

3. **Export ke Excel (Merapikan Struktur)**
    - Seluruh hasil filter (Penjualan & Stok) dapat di-export langsung ke format Excel (`.xlsx`).
    - Struktur kolom Excel akan otomatis menyesuaikan dengan format yang rapi dan terstandardisasi sesuai dengan filter tanggal/periode yang dipilih, sehingga siap untuk dicetak atau diarsipkan oleh _Owner_.

4. **Log Aktivitas User (Audit Trail)**
    - Sistem akan secara otomatis mencatat semua aktivitas yang dilakukan oleh semua _user_ (terutama Kasir) ke dalam tabel `activity_logs`.
    - Jejak rekam meliputi: jam pasti kasir _login_, percobaan _login_ gagal, transaksi keranjang (tambah/hapus barang), hingga sukses melakukan pembayaran pesanan.
    - Admin/Owner diberikan halaman khusus (Dashboard Log) untuk memantau setiap pergerakan kasir, menjamin transparansi dan mencegah tindakan mencurigakan.

---

## Fase 8: Pengaturan Sistem & Pemeliharaan (Khusus Admin/Owner)

Fase ini menampung fitur-fitur administratif untuk kustomisasi identitas toko, perlindungan data, dan kanal bantuan (_support_).

1. **Pengaturan Identitas Toko & Kustomisasi Struk**
    - Tersedia halaman "Pengaturan Toko" di mana Admin bisa mengganti **Nama Toko**, Alamat, Kontak, dan pesan _footer_ struk ("Terima kasih sudah berbelanja").
    - **Upload Logo**: Kemampuan untuk mengunggah logo toko yang nantinya akan otomatis tercetak di bagian atas _header_ struk belanja.

2. **Backup, Restore, & Export/Import Data**
    - **Backup & Restore**: Fitur proteksi satu klik untuk mengunduh arsip seluruh basis data (_database_) sebagai cadangan darurat, dan mengembalikannya (Restore) jika PC kasir/server mengalami _error_ kritis.
    - **Import Excel**: Admin bisa memasukkan 1000+ data barang sekaligus menggunakan _template_ Excel tanpa harus menginput manual satu per satu.

3. **Tombol "Request Feature" (Langsung ke WhatsApp Developer)**
    - Terdapat tombol akses cepat di menu Admin untuk _"Request Feature / Bantuan Teknis"_.
    - Tombol ini mengadopsi integrasi tautan langsung (wa.me) ke nomor WhatsApp Developer: **0816234185**.
    - Menggunakan _template_ teks otomatis (misal: _"Halo, saya pengguna Aplikasi POS dan saya ingin request fitur tambahan..."_), sehingga mempermudah proses komunikasi dan pengembangan (_maintenance_) ke depannya.
