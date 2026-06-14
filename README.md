# 🛒 Aplikasi POS (Point of Sale) Modern

Aplikasi Point of Sale (POS) modern berbasis web yang dirancang khusus untuk mempercepat transaksi kasir, memudahkan manajemen stok produk, dan memberikan laporan bisnis secara *real-time* kepada pemilik toko. Dibangun menggunakan **Laravel 11**, **Tailwind CSS v4 (Vite)**, dan **Livewire 3**.

---

## ⚡ Fitur Utama

Aplikasi POS ini dirancang dengan pembagian hak akses (role) yang ketat antara **Admin/Owner** dan **Kasir**:

### 1. Transaksi Kasir (Point of Sale) — *Untuk Kasir & Admin*
- **Katalog Produk Cepat:** Menampilkan katalog produk secara visual, dikelompokkan berdasarkan kategori.
- **Pencarian Pintar:** Cari produk secara cepat menggunakan keyboard atau *Barcode Scanner* langsung ke input SKU.
- **Keranjang Dinamis:** Pengelolaan keranjang real-time (tambah, kurangi kuantitas, hapus item) secara instan.
- **Kalkulasi Otomatis:** Menghitung subtotal, diskon (jika ada), Pajak/PPN default, dan uang kembalian secara otomatis.
- **Cetak Struk:** Cetak struk belanja thermal (lebar 58mm) dengan format rapi dan menyertakan logo toko serta footer custom.
- **Uji Alat Kasir:** Halaman diagnostik mandiri bagi kasir untuk menguji kompatibilitas printer kasir atau perangkat keras lainnya.

### 2. Dashboard Bisnis — *Khusus Admin*
- **Statistik Ringkas:** Menampilkan Total Pendapatan, Transaksi Hari Ini, Produk Terjual, dan Peringatan Stok Tipis.
- **Grafik Interaktif:** Visualisasi grafik garis/batang untuk performa omzet penjualan harian, bulanan, dan tahunan.
- **Daftar Transaksi Terbaru:** Pantau aktivitas transaksi terakhir yang terjadi di kasir.

### 3. Manajemen Master Data — *Khusus Admin*
- **Kategori:** CRUD kategori produk (makanan, minuman, barang pecah belah, dll.).
- **Supplier:** CRUD data pemasok barang lengkap dengan detail kontak.
- **Produk:** CRUD produk lengkap dengan SKU (bisa auto-generate 12-digit), harga beli (HPP), harga jual, perhitungan otomatis margin keuntungan, dan stok minimum.
- *Catatan:* Pengeditan jumlah stok secara manual pada form produk dinonaktifkan demi menjaga akuntabilitas audit data.

### 4. Manajemen Stok (Inventory) — *Khusus Admin*
- **Stok Masuk (Stock In):** Mencatat penambahan barang dari supplier atau retur barang.
- **Stok Keluar (Stock Out):** Mencatat pengurangan barang akibat rusak, kedaluwarsa, atau hilang.
- **Riwayat Mutasi:** Log riwayat mutasi stok produk yang mencatat riwayat masuk, keluar, dan pengurangan otomatis saat transaksi kasir berhasil.

### 5. Laporan & Aktivitas — *Khusus Admin*
- **Laporan Penjualan:** Rekap laporan penjualan harian/bulanan dengan filter rentang tanggal dan nama kasir. Laporan dapat diekspor langsung ke file Excel (.xlsx).
- **Laporan Stok:** Analisis nilai aset persediaan (total modal HPP) dan potensi omzet penjualan dari seluruh produk yang aktif.
- **Log Aktivitas (Security Audit):** Mencatat log aktivitas setiap pengguna secara terperinci (misal: login, logout, tambah produk, hapus transaksi, dll.).

### 6. Pengaturan Sistem & Pemeliharaan — *Khusus Admin*
- **Identitas Toko:** Atur nama toko, alamat, nomor telepon, website/sosial media, dan pesan footer struk.
- **Logo Toko:** Unggah logo toko dalam format PNG/JPG untuk dipasang pada bagian atas struk belanja.
- **Backup Database:** Satu tombol untuk mengunduh cadangan seluruh database sistem dalam format file SQL (.sql) untuk menjaga keamanan data.
- **Import Produk via Excel:** Import ribuan data produk sekaligus menggunakan berkas template Excel yang disediakan.
- **Bantuan & Dukungan Developer:** Hubungi developer secara langsung lewat tombol WhatsApp otomatis yang telah terintegrasi dengan template pesan custom.

---

## 🛠️ Tech Stack & Library

- **Framework Core:** [Laravel 11](https://laravel.com)
- **Frontend CSS:** [Tailwind CSS v4](https://tailwindcss.com) (dibundle via Vite)
- **State Management:** [Livewire 3](https://livewire.laravel.com) & Alpine.js
- **Icons:** FontAwesome 6 (CDN)
- **Alerts:** SweetAlert 2
- **Font:** Google Fonts (Inter)
- **Build Tool:** Vite v6

---

## 🚀 Panduan Instalasi (Lokal)

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi POS ini di server lokal Anda:

### 1. Prasyarat Sistem
Pastikan komputer Anda sudah terinstall:
- PHP >= 8.2 (dilengkapi ekstensi pdo, gd, mbstring, xml, curl, zip)
- Composer
- Node.js & NPM
- MySQL atau MariaDB

### 2. Kloning & Install Dependensi PHP
```bash
# Clone repository ini (atau extract folder)
# Masuk ke folder project
cd Aplikasi-POS

# Install seluruh dependensi Composer
composer install
```

### 3. Konfigurasi Environment (`.env`)
Salin file konfigurasi `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` yang baru dibuat dan sesuaikan konfigurasi database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=username_mysql_anda
DB_PASSWORD=password_mysql_anda
```

### 4. Generate Application Key & Migrasi Database
Jalankan perintah ini untuk membuat key enkripsi aplikasi dan membangun struktur tabel beserta data awal (seeders):
```bash
# Generate app key
php artisan key:generate

# Jalankan migrasi tabel database dan masukkan data demo awal
php artisan migrate --seed
```

### 5. Buat Link Simbolik Storage
Agar logo toko yang diupload di pengaturan dapat diakses secara publik di struk belanja, jalankan perintah berikut:
```bash
php artisan storage:link
```

### 6. Install & Compile Asset Frontend
Install dependensi JavaScript dan lakukan build produksi untuk asset CSS/JS:
```bash
# Install paket Node.js
npm install

# Compile asset produksi menggunakan Vite
npm run build
```
*(Catatan: Jika sedang dalam mode development dan ingin perubahan CSS langsung termuat secara real-time, jalankan `npm run dev` di terminal terpisah).*

### 7. Jalankan Server Laravel
Mulai web server lokal bawaan Laravel:
```bash
php artisan serve
```
Buka peramban (browser) Anda dan akses alamat: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🔑 Akun Login Demo

Gunakan akun awal di bawah ini setelah database berhasil di-seed:

| Peran (Role) | Username | Sandi (Password) | Hak Akses |
|---|---|---|---|
| **Admin Utama** | `admin` | `admin123` | Akses penuh (Dashboard, Master Data, Stok, Laporan, Pengaturan) |
| **Owner Toko** | `owner` | `owner123` | Akses penuh (Dashboard, Master Data, Stok, Laporan, Pengaturan) |
| **Kasir Utama** | `kasir1` | `kasir123` | Kasir (Hanya Point of Sale, Riwayat Transaksi, Uji Alat) |
| **Kasir Cadangan** | `kasir2` | `kasir123` | Kasir (Hanya Point of Sale, Riwayat Transaksi, Uji Alat) |

---

## 📂 Struktur Folder Utama (Penting)

Berikut berkas dan folder penting terkait fitur kustomisasi aplikasi ini:
- `routes/web.php` : Daftar seluruh rute halaman, termasuk rute kasir, laporan, dan pengaturan.
- `app/Http/Controllers/Admin/PengaturanController.php` : Pengontrol logika identitas toko, ekspor/impor, dan unduhan backup database SQL.
- `resources/views/admin/pengaturan/index.blade.php` : Tampilan antarmuka utama menu pengaturan sistem.
- `resources/views/admin/pengaturan/guide.blade.php` : Halaman buku petunjuk penggunaan aplikasi yang ramah pengguna.
- `resources/views/layouts/partials/sidebar.blade.php` : Navigasi sidebar dinamis berdasarkan peran masuk (Admin vs Kasir) yang sudah diwarnai menarik.

---

> **Catatan Pemeliharaan:** Sangat disarankan bagi pemilik toko (Admin) untuk rutin melakukan backup database (mengunduh berkas `.sql` di halaman Pengaturan) minimal seminggu sekali guna meminimalisir risiko kehilangan data transaksi akibat kegagalan perangkat keras lokal.
