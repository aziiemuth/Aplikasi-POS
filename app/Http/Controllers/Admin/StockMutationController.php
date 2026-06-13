<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMutation;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\Request;

/**
 * StockMutationController — Fase 3.3: Stok Masuk & Keluar yang Ketat
 *
 * HANYA bisa diakses oleh Admin (route sudah di-protect role:admin).
 * Kasir TIDAK bisa mengakses controller ini sama sekali.
 */
class StockMutationController extends Controller
{
    /**
     * Riwayat semua mutasi stok (kartu stok keseluruhan).
     */
    public function index(Request $request)
    {
        $mutations = StockMutation::with(['product', 'user', 'supplier', 'order'])
            ->when($request->product_id, fn($q) => $q->where('product_id', $request->product_id))
            ->when($request->tipe, fn($q) => $q->where('tipe', $request->tipe))
            ->when($request->date_from, fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to, fn($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $products = Product::active()->orderBy('nama_produk')->get(['id', 'nama_produk']);

        return view('admin.stock.index', compact('mutations', 'products'));
    }

    /**
     * Form stok masuk.
     */
    public function formMasuk()
    {
        $products  = Product::active()->with('category')->orderBy('nama_produk')->get();
        $suppliers = Supplier::active()->orderBy('nama_supplier')->get();

        return view('admin.stock.masuk', compact('products', 'suppliers'));
    }

    /**
     * === FASE 3.3 + 3.4: Proses Stok Masuk ===
     * Memanggil StockService yang otomatis menghitung HPP Rata-rata Tertimbang.
     */
    public function storeMasuk(Request $request)
    {
        $validated = $request->validate([
            'product_id'  => 'required|exists:products,id',
            'jumlah'      => 'required|integer|min:1',
            'harga_beli'  => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'keterangan'  => 'nullable|string|max:255',
        ], [
            'jumlah.min'     => 'Jumlah minimal 1 unit.',
            'harga_beli.min' => 'Harga beli tidak boleh negatif.',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        StockService::prosesStokMasuk(
            product:     $product,
            jumlah:      $validated['jumlah'],
            hargaBeli:   isset($validated['harga_beli']) ? (float) $validated['harga_beli'] : null,
            user:        auth()->user(),
            supplier:    isset($validated['supplier_id']) ? Supplier::find($validated['supplier_id']) : null,
            keterangan:  $validated['keterangan'] ?? null,
        );

        return redirect()->route('admin.stock.index')
            ->with('success', "Stok masuk <strong>+{$validated['jumlah']} {$product->satuan}</strong> untuk <strong>{$product->nama_produk}</strong> berhasil dicatat! HPP otomatis diperbarui.");
    }

    /**
     * Form stok keluar manual (admin only).
     */
    public function formKeluar()
    {
        $products = Product::active()->with('category')->orderBy('nama_produk')->get();

        return view('admin.stock.keluar', compact('products'));
    }

    /**
     * === FASE 3.3: Proses Stok Keluar Manual ===
     * Hanya untuk penyesuaian (barang rusak, hilang, dll).
     * Stok keluar karena penjualan ditangani otomatis di Fase 4.
     */
    public function storeKeluar(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'jumlah'     => 'required|integer|min:1',
            'keterangan' => 'required|string|max:255',
        ], [
            'keterangan.required' => 'Alasan stok keluar wajib diisi!',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Validasi stok cukup
        if ($product->stok_saat_ini < $validated['jumlah']) {
            return back()->withErrors([
                'jumlah' => "Stok tidak cukup! Stok {$product->nama_produk} saat ini: {$product->stok_saat_ini} {$product->satuan}."
            ])->withInput();
        }

        StockService::prosesStokKeluar(
            product:    $product,
            jumlah:     $validated['jumlah'],
            user:       auth()->user(),
            keterangan: $validated['keterangan'],
        );

        return redirect()->route('admin.stock.index')
            ->with('success', "Stok keluar <strong>-{$validated['jumlah']} {$product->satuan}</strong> untuk <strong>{$product->nama_produk}</strong> berhasil dicatat.");
    }

    /**
     * Riwayat mutasi stok untuk produk tertentu (kartu stok per produk).
     */
    public function perProduk(Product $product)
    {
        $mutations = StockMutation::with(['user', 'supplier', 'order'])
            ->where('product_id', $product->id)
            ->latest()
            ->paginate(20);

        return view('admin.stock.per-produk', compact('product', 'mutations'));
    }
}
