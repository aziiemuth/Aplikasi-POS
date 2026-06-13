<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Events\StockUpdated;

class PosTransactionService
{
    /**
     * Proses checkout: pindahkan cart ke order, record snapshot harga, potong stok, buat mutasi.
     * Menggunakan database transaction agar bulletproof.
     */
    public static function checkout(
        User $user,
        string $namaCustomer,
        string $metodePembayaran,
        float $jumlahBayar,
        float $diskonGlobal = 0,
        float $pajakPpn = 0,
        string $catatan = null,
        string $status = 'lunas'
    ): Order {
        $order = DB::transaction(function () use (
            $user, $namaCustomer, $metodePembayaran, $jumlahBayar, $diskonGlobal, $pajakPpn, $catatan, $status
        ) {
            $carts = Cart::where('user_id', $user->id)->with('product')->get();

            if ($carts->isEmpty()) {
                throw new Exception("Keranjang kosong, tidak bisa checkout.");
            }

            // 1. Hitung total
            $totalSebelumDiskon = 0;
            $itemsData = [];

            foreach ($carts as $cart) {
                $product = $cart->product;
                
                // Validasi anti-overselling (cegah stok minus)
                if ($product->stok_saat_ini < $cart->jumlah) {
                    throw new Exception("Stok produk {$product->nama_produk} tidak mencukupi. Tersedia: {$product->stok_saat_ini}, Diminta: {$cart->jumlah}");
                }

                $hargaItem = $product->harga_jual;
                $totalItem = ($hargaItem - $cart->diskon_item) * $cart->jumlah;
                $totalSebelumDiskon += $totalItem;

                $itemsData[] = [
                    'product_id'           => $product->id,
                    'nama_produk_snapshot' => $product->nama_produk,
                    'harga_jual_snapshot'  => $product->harga_jual,
                    'hpp_snapshot'         => $product->modal_hpp,
                    'diskon_item'          => $cart->diskon_item,
                    'jumlah'               => $cart->jumlah,
                    'total_harga_item'     => $totalItem,
                ];
            }

            $totalPembayaran = $totalSebelumDiskon - $diskonGlobal + $pajakPpn;
            $kembalian = $jumlahBayar - $totalPembayaran;

            if ($status === 'lunas' && $kembalian < 0) {
                throw new Exception("Uang pembayaran kurang!");
            }

            // 2. Buat Order
            $order = Order::create([
                'user_id'              => $user->id,
                'nomor_order'          => Order::generateNomorOrder(),
                'nama_customer'        => $namaCustomer,
                'total_sebelum_diskon' => $totalSebelumDiskon,
                'diskon_global'        => $diskonGlobal,
                'pajak_ppn'            => $pajakPpn,
                'total_pembayaran'     => $totalPembayaran,
                'metode_pembayaran'    => $metodePembayaran,
                'jumlah_bayar'         => $jumlahBayar,
                'kembalian'            => $kembalian < 0 ? 0 : $kembalian,
                'status'               => $status,
                'catatan'              => $catatan,
            ]);

            // 3. Masukkan OrderItems & Potong Stok
            foreach ($itemsData as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);

                $product = Product::find($item['product_id']);
                
                // Panggil StockService untuk memotong stok dan mencatat mutasi
                StockService::prosesStokKeluarPenjualan(
                    product: $product,
                    jumlah: $item['jumlah'],
                    orderId: $order->id,
                    userId: $user->id
                );
            }

            // 4. Bersihkan keranjang kasir ini
            Cart::where('user_id', $user->id)->delete();

            return $order;
        });

        // $order sekarang berisi hasil dari DB::transaction

        // 5. Broadcast perubahan stok ke kasir lain (di luar DB::transaction agar
        //    error Reverb tidak me-rollback transaksi yang sudah berhasil)
        try {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    broadcast(new StockUpdated($product->id, $product->stok_saat_ini))->toOthers();
                }
            }
        } catch (\Throwable $e) {
            // Reverb down tidak boleh menggagalkan transaksi
            \Illuminate\Support\Facades\Log::warning('Broadcast StockUpdated gagal: ' . $e->getMessage());
        }

        return $order;
    }
}
