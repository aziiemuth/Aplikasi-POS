<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StockMutationController;
use App\Http\Controllers\Admin\ToolsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Utama — Redirect ke Login
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('kasir.pos');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES — Fase 2.3: Login / Logout
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES — Hanya bisa diakses role 'admin'
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // === Dashboard ===
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // === Fase 2.2: Manajemen User ===
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/restore', [UserManagementController::class, 'restore'])->name('users.restore');
    Route::patch('/users/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggle-status');

    // =========================================================
    // === FASE 3.1: Master Data — Kategori ===
    // =========================================================
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::patch('/categories/{category}/toggle', [CategoryController::class, 'toggleStatus'])->name('categories.toggle');

    // =========================================================
    // === FASE 3.1: Master Data — Supplier (hidden dari Kasir) ===
    // =========================================================
    Route::resource('suppliers', SupplierController::class)->except(['show']);
    Route::patch('/suppliers/{supplier}/toggle', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggle');

    // =========================================================
    // === FASE 3.1 + 3.2: Master Data — Produk + Barcode ===
    // =========================================================
    // Fase 3.2: Generate SKU/Barcode unik 12-digit via AJAX
    Route::get('/products/generate-sku', [ProductController::class, 'generateSku'])->name('products.generate-sku');
    Route::resource('products', ProductController::class);
    // Hapus foto produk
    Route::delete('/products/{product}/foto', [ProductController::class, 'destroyFoto'])->name('products.destroy-foto');
    // Fase 3: Restore produk yang di-soft delete (withTrashed route binding diperlukan)
    Route::patch('/products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');

    // =========================================================
    // === FASE 3.3: Stok Masuk & Keluar yang Ketat ===
    // =========================================================
    // Riwayat semua mutasi stok
    Route::get('/stock', [StockMutationController::class, 'index'])->name('stock.index');
    // Form + proses stok masuk (admin only)
    Route::get('/stock/masuk', [StockMutationController::class, 'formMasuk'])->name('stock.masuk');
    Route::post('/stock/masuk', [StockMutationController::class, 'storeMasuk'])->name('stock.masuk.store');
    // Form + proses stok keluar manual (admin only)
    Route::get('/stock/keluar', [StockMutationController::class, 'formKeluar'])->name('stock.keluar');
    Route::post('/stock/keluar', [StockMutationController::class, 'storeKeluar'])->name('stock.keluar.store');
    // Riwayat mutasi per produk
    Route::get('/stock/produk/{product}', [StockMutationController::class, 'perProduk'])->name('stock.per-produk');

    // =========================================================
    // === FASE 7: Laporan Bisnis ===
    // =========================================================
    Route::get('/laporan/penjualan', [\App\Http\Controllers\Admin\LaporanController::class, 'penjualan'])->name('laporan.penjualan');
    Route::get('/laporan/penjualan/export', [\App\Http\Controllers\Admin\LaporanController::class, 'exportPenjualan'])->name('laporan.penjualan.export');
    Route::get('/laporan/stok', [\App\Http\Controllers\Admin\LaporanController::class, 'stok'])->name('laporan.stok');
    Route::get('/laporan/stok/export', [\App\Http\Controllers\Admin\LaporanController::class, 'exportStok'])->name('laporan.stok.export');
    Route::get('/laporan/activity-log', [\App\Http\Controllers\Admin\LaporanController::class, 'activityLog'])->name('laporan.activity-log');

    // =========================================================
    // === FASE 8: Pengaturan Sistem & Pemeliharaan ===
    // =========================================================
    Route::get('/pengaturan', [\App\Http\Controllers\Admin\PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::get('/pengaturan/panduan', [\App\Http\Controllers\Admin\PengaturanController::class, 'guide'])->name('pengaturan.guide');
    Route::post('/pengaturan/identitas', [\App\Http\Controllers\Admin\PengaturanController::class, 'updateIdentitas'])->name('pengaturan.identitas');
    Route::get('/pengaturan/backup', [\App\Http\Controllers\Admin\PengaturanController::class, 'backup'])->name('pengaturan.backup');
    Route::get('/pengaturan/import-template', [\App\Http\Controllers\Admin\PengaturanController::class, 'importTemplate'])->name('pengaturan.import.template');
    Route::post('/pengaturan/import-produk', [\App\Http\Controllers\Admin\PengaturanController::class, 'importProduk'])->name('pengaturan.import.produk');
    Route::post('/pengaturan/reset', [\App\Http\Controllers\Admin\PengaturanController::class, 'resetDatabase'])->name('pengaturan.reset');
    Route::post('/pengaturan/seed-dummy', [\App\Http\Controllers\Admin\PengaturanController::class, 'seedDummyTokoKelontong'])->name('pengaturan.seed-dummy');

});

/*
|--------------------------------------------------------------------------
| KASIR ROUTES — Hanya bisa diakses role 'kasir' DAN 'admin'
| FASE 3: Kasir TIDAK BISA mengakses route /admin/stock, /admin/products, dll
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:kasir,admin'])->prefix('kasir')->name('kasir.')->group(function () {

    // Halaman POS (dikembangkan di Fase 4)
    Route::get('/pos', \App\Livewire\Pos\PosCheckout::class)->name('pos');

    // Riwayat Transaksi Kasir
    Route::get('/riwayat', \App\Livewire\Kasir\RiwayatTransaksi::class)->name('riwayat');

    // Fase 6: Halaman Struk 58mm Khusus Cetak
    Route::get('/pos/struk/{order}', [\App\Http\Controllers\Kasir\OrderController::class, 'struk'])->name('struk');

    // Fase 5: Diagnostik Alat Kasir
    Route::get('/tools/diagnostik', [\App\Http\Controllers\Kasir\ToolsController::class, 'diagnostik'])->name('tools.diagnostik');

});

Route::get('/run-link', function () {
    Artisan::call('storage:link');
    return 'Storage link created successfully!';
});

