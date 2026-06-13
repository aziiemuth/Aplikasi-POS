<?php

namespace App\Livewire\Pos;

use App\Models\ActivityLog;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\CacheService;
use App\Services\PosTransactionService;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * PosCheckout — Livewire Component (Fase 4)
 *
 * Fitur:
 * - Katalog produk + filter kategori + search/barcode
 * - Keranjang belanja (tambah, kurangi, hapus item)
 * - Diskon per-item (Fase 4.4)
 * - Diskon global & pajak PPN
 * - Hold Bill / Open Bill (Fase 4.3) — tahan transaksi tanpa memotong stok
 * - Resume & Cancel Open Bill
 * - Proses Checkout dengan snapshot harga (Fase 4.5)
 * - Real-time stock sync via Laravel Reverb (Fase 4.5)
 * - Diagnostik alat (Fase 4.6)
 */
class PosCheckout extends Component
{
    // Filter katalog
    public $search = '';
    public $categoryId = null;

    // Modal pembayaran
    public $showPaymentModal = false;
    public $namaCustomer = 'Umum';
    public $metodePembayaran = 'Tunai';
    public $jumlahBayar = 0;
    public $catatan = '';

    // Diskon & pajak
    public $diskonGlobal = 0;
    public $pajakPpn = 0;

    // Open Bills
    public $openBills = [];
    public $showOpenBills = false;

    // Fase 6: Modal Struk
    public $showReceiptModal = false;
    public $receiptOrderId = null;

    // Dengarkan event StockUpdated dari Reverb
    public function getListeners()
    {
        return [
            "echo:pos,StockUpdated" => 'refreshProducts',
            "echo:pos,CatalogUpdated" => 'refreshCatalog',
        ];
    }

    public function refreshProducts()
    {
        // Re-render otomatis saat stok berubah dari kasir lain (anti-overselling)
        unset($this->products);
        unset($this->carts);
    }

    public function refreshCatalog()
    {
        // Re-render otomatis saat ada perubahan master data produk/kategori dari admin
        unset($this->products);
        unset($this->categories);
    }

    // ==========================================
    // COMPUTED PROPERTIES
    // ==========================================

    #[Computed]
    public function products()
    {
        $query = Product::active();

        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nama_produk', 'like', "%{$this->search}%")
                  ->orWhere('sku', 'like', "%{$this->search}%");
            });
        }

        return $query->orderBy('nama_produk')->get();
    }

    #[Computed]
    public function categories()
    {
        return CacheService::getCategories();
    }

    #[Computed]
    public function carts()
    {
        return Cart::where('user_id', auth()->id())->with('product')->get();
    }

    // ==========================================
    // FILTER KATEGORI
    // ==========================================

    public function setCategory($id)
    {
        $this->categoryId = $id;
        unset($this->products);
    }

    // ==========================================
    // KERANJANG BELANJA
    // ==========================================

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        // Fase 4.3: LARANGAN STOK MINUS
        if (!$product || $product->stok_saat_ini <= 0) {
            $this->dispatch('swal', title: 'Stok Kosong! 🚫', text: 'Barang ini tidak bisa ditambahkan karena stoknya habis.', icon: 'error');
            return;
        }

        $cart = Cart::where('user_id', auth()->id())->where('product_id', $productId)->first();

        if ($cart) {
            if ($cart->jumlah + 1 > $product->stok_saat_ini) {
                $this->dispatch('swal', title: 'Stok Tidak Cukup!', text: "Hanya tersisa {$product->stok_saat_ini} {$product->satuan} di gudang.", icon: 'warning');
                return;
            }
            $cart->increment('jumlah');
        } else {
            Cart::create([
                'user_id'     => auth()->id(),
                'product_id'  => $productId,
                'jumlah'      => 1,
                'diskon_item' => 0,
            ]);
        }

        unset($this->carts);
        $this->dispatch('play-beep');
    }

    public function updateQuantity($cartId, $action)
    {
        $cart = Cart::find($cartId);
        if (!$cart) return;

        if ($action === 'increase') {
            if ($cart->jumlah + 1 > $cart->product->stok_saat_ini) {
                $this->dispatch('swal', title: 'Stok Tidak Cukup!', text: "Sisa stok: {$cart->product->stok_saat_ini} {$cart->product->satuan}", icon: 'warning');
                return;
            }
            $cart->increment('jumlah');
        } elseif ($action === 'decrease') {
            if ($cart->jumlah > 1) {
                $cart->decrement('jumlah');
            } else {
                $cart->delete();
            }
        }

        unset($this->carts);
    }

    /**
     * === FASE 4.4: Update Diskon Per-Item ===
     */
    public function updateDiskonItem($cartId, $diskon)
    {
        $cart = Cart::find($cartId);
        if (!$cart) return;

        $diskon = max(0, (float) $diskon);

        if ($diskon >= $cart->product->harga_jual) {
            $this->dispatch('swal', title: 'Diskon Terlalu Besar!', text: 'Diskon tidak boleh melebihi harga jual produk.', icon: 'warning');
            return;
        }

        $cart->update(['diskon_item' => $diskon]);
        unset($this->carts);
    }

    public function removeItem($cartId)
    {
        Cart::where('id', $cartId)->delete();
        unset($this->carts);
    }

    public function clearCart()
    {
        Cart::where('user_id', auth()->id())->delete();
        unset($this->carts);
    }

    // ==========================================
    // HOLD BILL / OPEN BILL (FASE 4.3)
    // ==========================================

    /**
     * Tahan transaksi (Hold Bill) — simpan ke orders dengan status open_bill.
     * PENTING: Stok TIDAK terpotong saat hold. Stok baru terpotong saat checkout.
     */
    public function holdBill()
    {
        if ($this->carts->isEmpty()) {
            $this->dispatch('swal', title: 'Keranjang Kosong!', text: 'Tidak ada barang untuk di-hold.', icon: 'warning');
            return;
        }

        try {
            DB::transaction(function () {
                $subtotal        = $this->calculateSubtotal();
                $totalPembayaran = $subtotal - ($this->diskonGlobal ?: 0) + ($this->pajakPpn ?: 0);

                $order = Order::create([
                    'user_id'              => auth()->id(),
                    'nomor_order'          => Order::generateNomorOrder(),
                    'nama_customer'        => $this->namaCustomer ?: 'Umum',
                    'total_sebelum_diskon' => $subtotal,
                    'diskon_global'        => $this->diskonGlobal ?: 0,
                    'pajak_ppn'            => $this->pajakPpn ?: 0,
                    'total_pembayaran'     => $totalPembayaran,
                    'metode_pembayaran'    => $this->metodePembayaran,
                    'jumlah_bayar'         => 0,
                    'kembalian'            => 0,
                    'status'               => 'open_bill',
                    'catatan'              => $this->catatan ?: 'Hold Bill',
                ]);

                foreach ($this->carts as $cart) {
                    OrderItem::create([
                        'order_id'             => $order->id,
                        'product_id'           => $cart->product_id,
                        'nama_produk_snapshot' => $cart->product->nama_produk,
                        'harga_jual_snapshot'  => $cart->product->harga_jual,
                        'hpp_snapshot'         => $cart->product->modal_hpp,
                        'diskon_item'          => $cart->diskon_item,
                        'jumlah'               => $cart->jumlah,
                        'total_harga_item'     => ($cart->product->harga_jual - $cart->diskon_item) * $cart->jumlah,
                    ]);
                }

                // Kosongkan keranjang kasir
                Cart::where('user_id', auth()->id())->delete();
            });

            ActivityLog::log('Hold Bill', "Kasir menahan transaksi (Open Bill) untuk customer: " . ($this->namaCustomer ?: 'Umum'));
            $this->resetCheckoutForm();
            $this->loadOpenBills();
            $this->showOpenBills = true;
            unset($this->carts);

            $this->dispatch('swal', title: 'Bill Di-hold! ⏸', text: 'Transaksi disimpan. Kasir bisa melayani pelanggan berikutnya.', icon: 'info');

        } catch (Exception $e) {
            $this->dispatch('swal', title: 'Gagal Hold!', text: $e->getMessage(), icon: 'error');
        }
    }

    /**
     * Muat daftar open bill milik kasir ini.
     */
    public function loadOpenBills()
    {
        $this->openBills = Order::where('user_id', auth()->id())
            ->where('status', 'open_bill')
            ->with('items')
            ->latest()
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Lanjutkan open bill — pindahkan item dari order kembali ke keranjang.
     */
    public function resumeOpenBill($orderId)
    {
        $order = Order::with('items.product')->find($orderId);
        if (!$order || $order->user_id !== auth()->id() || $order->status !== 'open_bill') return;

        // Gabungkan dengan keranjang yang ada (atau timpa)
        Cart::where('user_id', auth()->id())->delete();

        foreach ($order->items as $item) {
            if ($item->product) {
                // Cek stok masih cukup
                $available = min($item->jumlah, $item->product->stok_saat_ini);
                if ($available > 0) {
                    Cart::create([
                        'user_id'     => auth()->id(),
                        'product_id'  => $item->product_id,
                        'jumlah'      => $available,
                        'diskon_item' => $item->diskon_item,
                    ]);
                }
            }
        }

        // Hapus order open bill beserta items
        $order->items()->delete();
        $order->delete();

        $this->loadOpenBills();
        $this->showOpenBills = false;
        unset($this->carts);

        $this->dispatch('swal', title: 'Bill Dilanjutkan! ▶', text: 'Item sudah kembali ke keranjang.', icon: 'success');
    }

    /**
     * Batalkan open bill.
     */
    public function cancelOpenBill($orderId)
    {
        $order = Order::find($orderId);
        if (!$order || $order->user_id !== auth()->id()) return;

        $order->items()->delete();
        $order->delete();

        $this->loadOpenBills();
        $this->dispatch('swal', title: 'Open Bill Dibatalkan', text: 'Transaksi open bill telah dihapus.', icon: 'warning');
    }

    public function toggleOpenBills()
    {
        $this->showOpenBills = !$this->showOpenBills;
        if ($this->showOpenBills) {
            $this->loadOpenBills();
        }
    }

    // ==========================================
    // CHECKOUT / PEMBAYARAN
    // ==========================================

    public function openPaymentModal()
    {
        if ($this->carts->isEmpty()) {
            $this->dispatch('swal', title: 'Keranjang Kosong!', text: 'Tambahkan barang terlebih dahulu.', icon: 'warning');
            return;
        }

        $subtotal = $this->calculateSubtotal();
        $this->jumlahBayar = max(0, $subtotal - ($this->diskonGlobal ?: 0) + ($this->pajakPpn ?: 0));
        $this->showPaymentModal = true;
    }

    private function calculateSubtotal(): float
    {
        return (float) $this->carts->sum(fn ($c) => ($c->product->harga_jual - $c->diskon_item) * $c->jumlah);
    }

    public function processCheckout()
    {
        $this->validate([
            'jumlahBayar' => 'required|numeric|min:0',
        ]);

        try {
            $order = PosTransactionService::checkout(
                user: auth()->user(),
                namaCustomer: $this->namaCustomer ?: 'Umum',
                metodePembayaran: $this->metodePembayaran,
                jumlahBayar: (float) $this->jumlahBayar,
                diskonGlobal: (float) ($this->diskonGlobal ?: 0),
                pajakPpn: (float) ($this->pajakPpn ?: 0),
                catatan: $this->catatan
            );

            $this->showPaymentModal = false;
            $this->resetCheckoutForm();
            unset($this->carts);

            $kembalian = number_format($order->kembalian, 0, ',', '.');
            $this->dispatch('swal',
                title: 'Transaksi Berhasil! 🎉',
                text: "Order #{$order->nomor_order} | Kembalian: Rp {$kembalian}",
                icon: 'success'
            );

            // Fase 6: Tampilkan Modal Struk
            $this->receiptOrderId = $order->id;
            $this->showReceiptModal = true;

            // Trigger event untuk JS listener (jika diperlukan)
            $this->dispatch('print-struk', orderId: $order->id);

        } catch (Exception $e) {
            $this->dispatch('swal', title: 'Checkout Gagal!', text: $e->getMessage(), icon: 'error');
        }
    }

    private function resetCheckoutForm(): void
    {
        $this->namaCustomer     = 'Umum';
        $this->metodePembayaran = 'Tunai';
        $this->jumlahBayar      = 0;
        $this->catatan          = '';
        $this->diskonGlobal     = 0;
        $this->pajakPpn         = 0;
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->receiptOrderId = null;
    }

    // ==========================================
    // FASE 5: BARCODE SCANNER
    // ==========================================

    public function handleBarcodeScan($sku)
    {
        $product = Product::active()->where('sku', $sku)->first();

        if ($product) {
            $this->addToCart($product->id);
            // play-beep is dispatched inside addToCart
        } else {
            $this->dispatch('swal', title: 'Tidak Ditemukan', text: "Produk dengan barcode {$sku} tidak ditemukan.", icon: 'error');
        }
    }

    // ==========================================
    // LIFECYCLE
    // ==========================================

    public function mount()
    {
        $this->loadOpenBills();
    }

    public function render()
    {
        return view('livewire.pos.pos-checkout', [
            'products'   => $this->products,
            'categories' => $this->categories,
            'carts'      => $this->carts,
            'subtotal'   => $this->calculateSubtotal(),
        ])->layout('layouts.app');
    }
}
