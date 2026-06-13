<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\StockMutation;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * StockService — Fase 3.3 & 3.4
 *
 * Mengelola logika bisnis mutasi stok dengan ketat:
 * - Stok masuk: hitung ulang HPP rata-rata tertimbang & auto-update produk
 * - Stok keluar manual: kurangi stok dengan validasi
 * - Stok keluar dari transaksi POS: dipanggil dari OrderService (Fase 4)
 */
class StockService
{
    /**
     * === FASE 3.3 + 3.4: PROSES STOK MASUK ===
     *
     * Mencatat stok masuk ke kartu stok dan menghitung ulang HPP
     * menggunakan metode rata-rata tertimbang (Weighted Average Cost).
     *
     * Rumus HPP Baru:
     * HPP_baru = ((stok_lama × hpp_lama) + (qty_masuk × harga_beli)) / (stok_lama + qty_masuk)
     *
     * @param  Product       $product    Produk yang stoknya masuk
     * @param  int           $jumlah     Kuantitas yang masuk
     * @param  float|null    $hargaBeli  Harga beli per unit (null = tidak mengubah HPP)
     * @param  User          $user       Admin yang mencatat
     * @param  Supplier|null $supplier   Pemasok asal barang
     * @param  string|null   $keterangan Catatan mutasi
     *
     * @return StockMutation Record mutasi yang baru dibuat
     */
    public static function prosesStokMasuk(
        Product $product,
        int $jumlah,
        ?float $hargaBeli = null,
        ?User $user = null,
        ?Supplier $supplier = null,
        ?string $keterangan = null,
    ): StockMutation {
        return DB::transaction(function () use ($product, $jumlah, $hargaBeli, $user, $supplier, $keterangan) {

            $stokSebelum = $product->stok_saat_ini;
            $stokSesudah = $stokSebelum + $jumlah;

            // =====================================================
            // FASE 3.4: Hitung HPP Rata-rata Tertimbang
            // =====================================================
            $hppLama   = (float) $product->modal_hpp;
            $hppBaru   = $hppLama; // default: tetap sama

            if ($hargaBeli !== null && $hargaBeli > 0) {
                if ($stokSebelum <= 0) {
                    // Stok sebelumnya kosong → HPP langsung = harga beli baru
                    $hppBaru = $hargaBeli;
                } else {
                    // Rata-rata tertimbang
                    $hppBaru = (($stokSebelum * $hppLama) + ($jumlah * $hargaBeli)) / $stokSesudah;
                }

                // Auto-update HPP di master produk (Fase 3.4: Pembaruan Otomatis)
                $product->modal_hpp = round((float) $hppBaru, 2);
            }

            // Update stok produk
            $product->stok_saat_ini = $stokSesudah;
            $product->save();

            // =====================================================
            // FASE 3.3: Catat ke tabel stock_mutations (kartu stok)
            // =====================================================
            $mutation = StockMutation::create([
                'product_id'    => $product->id,
                'user_id'       => $user?->id ?? auth()->id(),
                'order_id'      => null,
                'supplier_id'   => $supplier?->id,
                'tipe'          => 'masuk',
                'jumlah'        => $jumlah,
                'stok_sebelum'  => $stokSebelum,
                'stok_sesudah'  => $stokSesudah,
                'harga_beli'    => $hargaBeli,
                'keterangan'    => $keterangan ?? 'Stok masuk manual oleh Admin',
            ]);

            // Catat ke activity log
            $hppInfo = ($hargaBeli > 0)
                ? "HPP diupdate: Rp " . number_format($hppLama, 0, ',', '.') . " → Rp " . number_format($hppBaru, 0, ',', '.')
                : "HPP tidak berubah";

            ActivityLog::log(
                'Stok Masuk',
                "[{$product->nama_produk}] +{$jumlah} unit. Stok: {$stokSebelum} → {$stokSesudah}. {$hppInfo}",
                $product
            );

            // Invalidate cache produk
            CacheService::forgetProductsByCategory((int) $product->category_id);

            return $mutation;
        });
    }

    /**
     * === FASE 3.3: PROSES STOK KELUAR MANUAL (Admin) ===
     *
     * Hanya untuk penyesuaian manual oleh Admin (misal: barang rusak/hilang).
     * Stok keluar karena terjual ditangani oleh OrderService (Fase 4).
     *
     * @param  Product     $product
     * @param  int         $jumlah
     * @param  User|null   $user
     * @param  string|null $keterangan
     *
     * @return StockMutation
     * @throws \Exception Jika stok tidak cukup
     */
    public static function prosesStokKeluar(
        Product $product,
        int $jumlah,
        ?User $user = null,
        ?string $keterangan = null,
    ): StockMutation {
        return DB::transaction(function () use ($product, $jumlah, $user, $keterangan) {

            // === Fase 3.3: LARANGAN STOK MINUS ===
            if ($product->stok_saat_ini < $jumlah) {
                throw new \Exception(
                    "Stok {$product->nama_produk} tidak mencukupi! " .
                    "Tersedia: {$product->stok_saat_ini}, diminta: {$jumlah}."
                );
            }

            $stokSebelum = $product->stok_saat_ini;
            $stokSesudah = $stokSebelum - $jumlah;

            // Update stok
            $product->stok_saat_ini = $stokSesudah;
            $product->save();

            // Catat mutasi
            $mutation = StockMutation::create([
                'product_id'   => $product->id,
                'user_id'      => $user?->id ?? auth()->id(),
                'order_id'     => null,
                'supplier_id'  => null,
                'tipe'         => 'keluar',
                'jumlah'       => $jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'harga_beli'   => null,
                'keterangan'   => $keterangan ?? 'Penyesuaian stok manual oleh Admin',
            ]);

            ActivityLog::log(
                'Stok Keluar Manual',
                "[{$product->nama_produk}] -{$jumlah} unit. Stok: {$stokSebelum} → {$stokSesudah}.",
                $product
            );

            CacheService::forgetProductsByCategory((int) $product->category_id);

            return $mutation;
        });
    }

    /**
     * === FASE 4 (dipanggil dari OrderService saat checkout) ===
     * Proses stok keluar otomatis karena transaksi penjualan.
     * HPP tidak berubah saat stok keluar.
     *
     * @param  Product $product
     * @param  int     $jumlah
     * @param  int     $orderId
     * @param  int     $userId
     *
     * @return StockMutation
     */
    public static function prosesStokKeluarPenjualan(
        Product $product,
        int $jumlah,
        int $orderId,
        int $userId,
    ): StockMutation {
        return DB::transaction(function () use ($product, $jumlah, $orderId, $userId) {

            if ($product->stok_saat_ini < $jumlah) {
                throw new \Exception("Stok {$product->nama_produk} tidak mencukupi untuk transaksi!");
            }

            $stokSebelum = $product->stok_saat_ini;
            $stokSesudah = $stokSebelum - $jumlah;

            $product->stok_saat_ini = $stokSesudah;
            $product->save();

            $mutation = StockMutation::create([
                'product_id'   => $product->id,
                'user_id'      => $userId,
                'order_id'     => $orderId,
                'supplier_id'  => null,
                'tipe'         => 'keluar',
                'jumlah'       => $jumlah,
                'stok_sebelum' => $stokSebelum,
                'stok_sesudah' => $stokSesudah,
                'harga_beli'   => null,
                'keterangan'   => "Stok keluar: Terjual via Order #{$orderId}",
            ]);

            CacheService::forgetProductsByCategory((int) $product->category_id);

            return $mutation;
        });
    }
}
