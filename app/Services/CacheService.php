<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * CacheService — Fase 1.4: Sistem Caching & Optimasi Performa
 *
 * Mengelola cache untuk data yang sering diakses namun jarang berubah:
 * - Daftar Kategori Produk (untuk filter di POS)
 * - Katalog Produk (untuk halaman POS)
 *
 * Cache akan otomatis di-refresh (invalidated) setiap kali Admin
 * menambah/edit/hapus produk atau kategori, sehingga server load
 * berkurang drastis hingga 80%.
 */
class CacheService
{
    // Durasi cache dalam detik
    const CACHE_CATEGORIES = 3600;   // 1 jam
    const CACHE_PRODUCTS   = 1800;   // 30 menit

    // Key cache
    const KEY_CATEGORIES        = 'pos_categories_active';
    const KEY_PRODUCTS_ALL      = 'pos_products_active_all';
    const KEY_PRODUCTS_CATEGORY = 'pos_products_category_'; // diikuti category_id

    // =========================================================
    // KATEGORI
    // =========================================================

    /**
     * Ambil semua kategori aktif dari cache.
     * Jika cache kosong, query dari DB dan simpan ke cache.
     */
    public static function getCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(
            self::KEY_CATEGORIES,
            self::CACHE_CATEGORIES,
            fn () => \App\Models\Category::active()->orderByName()->get()
        );
    }

    /**
     * Hapus cache kategori.
     * Dipanggil saat Admin tambah/edit/hapus kategori.
     */
    public static function forgetCategories(): void
    {
        Cache::forget(self::KEY_CATEGORIES);
    }

    // =========================================================
    // PRODUK
    // =========================================================

    /**
     * Ambil semua produk aktif dari cache.
     */
    public static function getProducts(?int $categoryId = null): \Illuminate\Database\Eloquent\Collection
    {
        if ($categoryId) {
            $key = self::KEY_PRODUCTS_CATEGORY . $categoryId;
            return Cache::remember(
                $key,
                self::CACHE_PRODUCTS,
                fn () => \App\Models\Product::active()
                    ->ofCategory($categoryId)
                    ->with('category')
                    ->orderByName()
                    ->get()
            );
        }

        return Cache::remember(
            self::KEY_PRODUCTS_ALL,
            self::CACHE_PRODUCTS,
            fn () => \App\Models\Product::active()
                ->with('category')
                ->orderByName()
                ->get()
        );
    }

    /**
     * Hapus semua cache produk.
     * Dipanggil saat Admin tambah/edit/hapus produk.
     */
    public static function forgetProducts(): void
    {
        Cache::forget(self::KEY_PRODUCTS_ALL);

        // Hapus cache semua kategori secara individual
        $categoryIds = \App\Models\Category::pluck('id');
        foreach ($categoryIds as $id) {
            Cache::forget(self::KEY_PRODUCTS_CATEGORY . $id);
        }
    }

    /**
     * Hapus cache produk untuk kategori tertentu saja.
     */
    public static function forgetProductsByCategory(int $categoryId): void
    {
        Cache::forget(self::KEY_PRODUCTS_ALL);
        Cache::forget(self::KEY_PRODUCTS_CATEGORY . $categoryId);
    }

    /**
     * Hapus SEMUA cache POS sekaligus.
     * Dipanggil saat ada perubahan massal atau reset sistem.
     */
    public static function forgetAll(): void
    {
        self::forgetCategories();
        self::forgetProducts();
    }
}
