<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ActivityLogger — Fase 2.1: Audit Trail Otomatis
 *
 * Middleware ini mencatat setiap request penting (POST/PUT/PATCH/DELETE)
 * ke tabel activity_logs untuk keperluan audit oleh Admin/Owner.
 *
 * Catatan: GET request tidak dicatat agar tidak membanjiri log table.
 */
class ActivityLogger
{
    /**
     * Aksi-aksi yang WAJIB dicatat meskipun GET.
     */
    protected array $alwaysLogRoutes = [
        'login',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Catat hanya jika user sudah login
        if (! auth()->check()) {
            return $response;
        }

        // Abaikan request otomatis dari background service seperti Livewire
        if ($request->is('livewire*') || $request->is('_debugbar*')) {
            return $response;
        }

        // Catat setiap metode yang mengubah data jika belum dicatat secara manual oleh controller
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            if (! ActivityLog::$hasLoggedManual) {
                $this->logActivity($request, $response);
            }
        }

        return $response;
    }

    private function logActivity(Request $request, Response $response): void
    {
        try {
            $routeName  = $request->route()?->getName() ?? 'unknown';
            $method     = $request->method();
            $url        = $request->path();
            $statusCode = $response->getStatusCode();

            // Mapping route/url ke bahasa sehari-hari yang ramah
            $routeMapping = [
                'login.post' => [
                    'aksi' => 'Login Berhasil',
                    'deskripsi' => 'Pengguna berhasil masuk ke dalam sistem.',
                ],
                'logout' => [
                    'aksi' => 'Logout',
                    'deskripsi' => 'Pengguna keluar dari sistem.',
                ],
                'admin.users.store' => [
                    'aksi' => 'Tambah User',
                    'deskripsi' => 'Menambahkan akun pengguna baru.',
                ],
                'admin.users.update' => [
                    'aksi' => 'Edit User',
                    'deskripsi' => 'Mengubah informasi atau status akun pengguna.',
                ],
                'admin.users.destroy' => [
                    'aksi' => 'Hapus User',
                    'deskripsi' => 'Menghapus akun pengguna dari sistem.',
                ],
                'admin.users.restore' => [
                    'aksi' => 'Restore User',
                    'deskripsi' => 'Memulihkan kembali akun pengguna yang dihapus.',
                ],
                'admin.users.toggle-status' => [
                    'aksi' => 'Ubah Status User',
                    'deskripsi' => 'Mengubah status aktif/nonaktif akun pengguna.',
                ],
                'admin.categories.store' => [
                    'aksi' => 'Tambah Kategori',
                    'deskripsi' => 'Menambahkan kategori produk baru.',
                ],
                'admin.categories.update' => [
                    'aksi' => 'Edit Kategori',
                    'deskripsi' => 'Mengubah nama atau informasi kategori produk.',
                ],
                'admin.categories.destroy' => [
                    'aksi' => 'Hapus Kategori',
                    'deskripsi' => 'Menghapus kategori produk dari sistem.',
                ],
                'admin.categories.toggle' => [
                    'aksi' => 'Ubah Status Kategori',
                    'deskripsi' => 'Mengubah status aktif/nonaktif kategori produk.',
                ],
                'admin.suppliers.store' => [
                    'aksi' => 'Tambah Supplier',
                    'deskripsi' => 'Menambahkan supplier baru.',
                ],
                'admin.suppliers.update' => [
                    'aksi' => 'Edit Supplier',
                    'deskripsi' => 'Mengubah informasi supplier.',
                ],
                'admin.suppliers.destroy' => [
                    'aksi' => 'Hapus Supplier',
                    'deskripsi' => 'Menghapus supplier dari sistem.',
                ],
                'admin.suppliers.toggle' => [
                    'aksi' => 'Ubah Status Supplier',
                    'deskripsi' => 'Mengubah status aktif/nonaktif supplier.',
                ],
                'admin.products.store' => [
                    'aksi' => 'Tambah Produk',
                    'deskripsi' => 'Menambahkan produk baru ke katalog.',
                ],
                'admin.products.update' => [
                    'aksi' => 'Edit Produk',
                    'deskripsi' => 'Mengubah informasi detail produk.',
                ],
                'admin.products.destroy' => [
                    'aksi' => 'Hapus Produk',
                    'deskripsi' => 'Menghapus produk dari katalog.',
                ],
                'admin.products.destroy-foto' => [
                    'aksi' => 'Hapus Foto Produk',
                    'deskripsi' => 'Menghapus gambar/foto dari produk.',
                ],
                'admin.products.restore' => [
                    'aksi' => 'Restore Produk',
                    'deskripsi' => 'Memulihkan kembali produk yang telah dihapus.',
                ],
                'admin.stock.masuk.store' => [
                    'aksi' => 'Stok Masuk',
                    'deskripsi' => 'Mencatat penambahan stok barang masuk.',
                ],
                'admin.stock.keluar.store' => [
                    'aksi' => 'Stok Keluar Manual',
                    'deskripsi' => 'Mencatat pengurangan stok barang keluar secara manual.',
                ],
            ];

            if (isset($routeMapping[$routeName])) {
                $aksi = $routeMapping[$routeName]['aksi'];
                $deskripsi = $routeMapping[$routeName]['deskripsi'];
                
                if ($statusCode >= 400) {
                    $deskripsi .= " (Gagal dengan status: {$statusCode})";
                }
            } else {
                // Fallback dinamis jika route tidak ada di mapping, tapi tetap ramah
                $friendlyMethod = match ($method) {
                    'POST' => 'Menambah data',
                    'PUT', 'PATCH' => 'Mengubah data',
                    'DELETE' => 'Menghapus data',
                    default => 'Aktivitas sistem',
                };
                
                // Terjemahkan path URL menjadi lebih bersih
                $cleanPath = str_replace('admin/', '', $url);
                $cleanPath = preg_replace('/\d+/', '', $cleanPath); // hilangkan angka ID
                $cleanPath = trim(str_replace('/', ' ', $cleanPath));
                
                $aksi = ucwords($friendlyMethod . ' ' . ($cleanPath ?: 'Sistem'));
                $deskripsi = "Memproses permintaan {$method} pada halaman {$url} (Status: {$statusCode})";
            }

            ActivityLog::log(
                aksi: $aksi,
                deskripsi: $deskripsi,
            );
        } catch (\Throwable $e) {
            // Jangan sampai error logging menghentikan request utama
            \Illuminate\Support\Facades\Log::warning('ActivityLogger gagal: ' . $e->getMessage());
        }
    }
}
