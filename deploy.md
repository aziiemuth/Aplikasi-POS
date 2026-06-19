# Panduan Deployment Aplikasi POS ke IDCloudHost (Shared Hosting)

Karena Anda menggunakan **Shared Hosting (cPanel/CyberPanel)** di IDCloudHost dan fitur Laravel Reverb dinonaktifkan, berikut adalah langkah-langkah *bulletproof* untuk memindahkan aplikasi POS Anda dari localhost ke server online.

---

## TAHAP 1: Persiapan di Komputer Lokal (Localhost)

1. **Jalankan Build Frontend (Wajib)**
   Buka terminal di dalam folder project Anda (`Aplikasi-POS`), lalu jalankan perintah ini untuk mengkompilasi file CSS dan JavaScript (Vite) ke mode produksi:
   ```bash
   npm run build
   ```
   *Tunggu sampai selesai. Perintah ini akan membuat folder `public/build`.*

2. **Buat File ZIP**
   Pilih semua file dan folder di dalam project Anda (`app`, `bootstrap`, `config`, `public`, `resources`, `routes`, `storage`, `.env`, dll), lalu jadikan satu file ZIP (misalnya `aplikasi-pos.zip`).
   *Catatan: Jangan lupa menyertakan file tersembunyi seperti `.env`.*

3. **Export Database (Backup SQL)**
   - Buka `localhost/phpmyadmin`.
   - Pilih database aplikasi POS Anda.
   - Klik tab **Export** lalu klik **Go** / **Kirim**.
   - Simpan file `.sql` tersebut di komputer Anda.

---

## TAHAP 2: Upload File ke Shared Hosting

1. **Login ke cPanel IDCloudHost** Anda.
2. Buka **File Manager**.
3. Buat folder baru di luar folder `public_html` untuk menyimpan file inti Laravel agar aman. 
   - Klik `+ Folder` di pojok kiri atas.
   - Beri nama folder: `pos-core` (atau nama lain).
4. Masuk ke dalam folder `pos-core` tersebut.
5. Klik **Upload** dan unggah file `aplikasi-pos.zip` dari komputer Anda.
6. Setelah selesai, klik kanan file `aplikasi-pos.zip` lalu pilih **Extract**.

---

## TAHAP 3: Mengatur Folder Public & Index.php

Karena struktur Shared Hosting berbeda, kita harus memisahkan folder `public` milik Laravel ke folder `public_html` milik server.

1. Masuk ke folder `pos-core` yang tadi di-extract.
2. Cari folder bernama **`public`**. Masuk ke dalamnya.
3. **Pilih semua file dan folder** di dalam folder `public` (termasuk `index.php`, `.htaccess`, folder `build`, folder `storage`, dll).
4. Klik **Move** (Pindah) dan arahkan pindahannya ke folder `/public_html`.
5. Buka folder `public_html`, cari file **`index.php`**, lalu klik kanan dan pilih **Edit**.
6. Ubah dua baris path ini agar mengarah ke folder `pos-core` yang kita buat tadi:

   **Cari kode ini:**
   ```php
   require __DIR__.'/../storage/framework/maintenance.php';
   ```
   **Ubah menjadi:**
   ```php
   require __DIR__.'/../pos-core/storage/framework/maintenance.php';
   ```

   **Cari kode ini:**
   ```php
   require __DIR__.'/../vendor/autoload.php';
   ```
   **Ubah menjadi:**
   ```php
   require __DIR__.'/../pos-core/vendor/autoload.php';
   ```

   **Cari kode ini:**
   ```php
   $app = require_once __DIR__.'/../bootstrap/app.php';
   ```
   **Ubah menjadi:**
   ```php
   $app = require_once __DIR__.'/../pos-core/bootstrap/app.php';
   ```
   
   *Simpan perubahan file `index.php`.*

---

## TAHAP 4: Persiapan Database di Hosting

1. Kembali ke beranda cPanel, cari menu **MySQL Databases**.
2. **Buat Database Baru**: Masukkan nama (misal: `posdb`), klik Create.
3. **Buat User Baru**: Masukkan nama user (misal: `posuser`) dan buat password yang kuat. Klik Create User.
4. **Hubungkan User ke Database**: Di bagian *Add User to Database*, pilih user dan database yang baru dibuat, lalu klik Add. Centang **ALL PRIVILEGES**, lalu klik Make Changes.
5. Kembali ke cPanel, buka **phpMyAdmin**.
6. Pilih database yang baru Anda buat, klik tab **Import**.
7. Pilih file `.sql` dari komputer Anda (hasil export di Tahap 1), lalu klik **Go**.

---

## TAHAP 5: Konfigurasi .env (Sangat Penting)

1. Buka **File Manager**, masuk ke folder `pos-core`.
2. Edit file **`.env`** dan sesuaikan baris-baris berikut:

   ```env
   # Wajib Production dan Debug dimatikan
   APP_ENV=production
   APP_DEBUG=false
   
   # Isi dengan domain website Anda
   APP_URL=https://domainanda.com
   
   # Pengaturan Database (sesuai yang dibuat di Tahap 4)
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_db_yang_dibuat_di_cpanel
   DB_USERNAME=user_db_yang_dibuat_di_cpanel
   DB_PASSWORD=password_db_yang_dibuat
   
   # Matikan Reverb & Redis (Karena Shared Hosting)
   BROADCAST_CONNECTION=log
   CACHE_STORE=file
   SESSION_DRIVER=file
   QUEUE_CONNECTION=sync
   ```
   *Simpan perubahan `.env`.*

---

## TAHAP 6: Memperbaiki Link Storage (Gambar Produk & Logo)

Karena fitur upload gambar (logo, foto produk) menggunakan storage symlink, dan Anda tidak punya akses terminal/SSH di Shared Hosting biasa, kita harus membuat symlink menggunakan file PHP.

1. Buka browser Anda, lalu kunjungi URL berikut (saya sudah buatkan route-nya di kodingan):
   
   **`https://domainanda.com/run-link`**

2. Jika layar menampilkan teks **"Storage link created successfully!"**, berarti folder storage sudah terhubung dengan benar.

---

## TAHAP 7: Selesai! 🎉

Coba akses domain Anda (misal `https://domainanda.com`). Aplikasi POS seharusnya sudah berjalan dengan lancar.

### Mengatasi Jika Terjadi Error 500
Jika Anda melihat error "500 Server Error" atau blank putih:
1. Cek file `pos-core/storage/logs/laravel.log` via File Manager, scroll paling bawah untuk melihat error aslinya.
2. Pastikan versi PHP di cPanel minimal **PHP 8.2**.
3. Pastikan ekstensi PHP di cPanel (di menu *Select PHP Version* -> *Extensions*) sudah mencentang: `fileinfo`, `pdo_mysql`, `mbstring`, `zip`, `gd`.
