<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Product;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * ProductController — Fase 3.1 + 3.2
 * - CRUD Produk (kategori, satuan, HPP, harga jual, foto, SKU)
 * - Generate SKU/Barcode unik 12-digit otomatis (Fase 3.2)
 * - Upload & hapus foto produk
 * - Cache invalidation setiap perubahan
 */
class ProductController extends Controller
{
    /**
     * Daftar semua produk termasuk yang soft-deleted.
     */
    public function index(Request $request)
    {
        $colNamaProduk = 'nama_produk';
        $colSku = 'sku';
        $colCategoryId = 'category_id';
        $colStokSaatIni = 'stok_saat_ini';
        $colNamaKategori = 'nama_kategori';

        $products = Product::withTrashed()
            ->with('category')
            ->when($request->input('search'), fn($q) => $q->where(function ($q) use ($request, $colNamaProduk, $colSku) {
                $q->where($colNamaProduk, 'like', "%{$request->input('search')}%")
                  ->orWhere($colSku, 'like', "%{$request->input('search')}%");
            }))
            ->when($request->input('category'), fn($q) => $q->where($colCategoryId, $request->input('category')))
            ->when($request->input('stok') === 'tipis', fn($q) => $q->active()->whereColumn($colStokSaatIni, '<=', 'stok_minimum'))
            ->when($request->input('stok') === 'kosong', fn($q) => $q->active()->where($colStokSaatIni, 0))
            ->orderByRaw('deleted_at IS NOT NULL, nama_produk ASC')
            ->paginate(15)
            ->withQueryString();

        if ($request->ajax()) {
            return view('admin.products._table', compact('products'))->render();
        }

        $categories = Category::active()->orderBy($colNamaKategori)->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Form tambah produk.
     */
    public function create()
    {
        $colNamaKategori = 'nama_kategori';
        $categories = Category::active()->orderBy($colNamaKategori)->get();
        $satuan = $this->getSatuanList();

        return view('admin.products.create', compact('categories', 'satuan'));
    }

    /**
     * Simpan produk baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'  => 'nullable|exists:categories,id',
            'sku'          => 'required|string|max:50|unique:products,sku',
            'nama_produk'  => 'required|string|max:200',
            'deskripsi'    => 'nullable|string',
            'satuan'       => 'required|string|max:30',
            'modal_hpp'    => 'required|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'stok_saat_ini'=> 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'    => 'boolean',
        ], [
            'sku.unique'       => 'SKU/Barcode sudah digunakan produk lain!',
            'foto.image'       => 'File harus berupa gambar.',
            'foto.max'         => 'Ukuran foto maksimal 2MB.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        // Upload foto
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('products', 'public');
        }

        $product = Product::create($validated);

        // FASE 3.3 BUGFIX: Buat mutasi stok awal jika stok > 0 agar riwayat kartu stok akurat
        if ($product->stok_saat_ini > 0) {
            \App\Models\StockMutation::create([
                'product_id'   => $product->id,
                'user_id'      => auth()->id(),
                'tipe'         => 'masuk',
                'jumlah'       => $product->stok_saat_ini,
                'stok_sebelum' => 0,
                'stok_sesudah' => $product->stok_saat_ini,
                'harga_beli'   => $product->modal_hpp,
                'keterangan'   => 'Stok awal saat penambahan produk baru',
            ]);
        }

        // Fase 1.4: Invalidate cache
        CacheService::forgetProducts();
        CacheService::forgetCategories();

        \App\Events\CatalogUpdated::dispatch();

        ActivityLog::log('Tambah Produk', "Admin menambah produk: [{$product->nama_produk}] SKU: {$product->sku}", $product);

        return redirect()->route('admin.products.show', $product)
            ->with('success', "Produk <strong>{$product->nama_produk}</strong> berhasil ditambahkan!");
    }

    /**
     * Halaman detail produk (+ barcode visual + riwayat stok ringkas).
     */
    public function show(Product $product)
    {
        $product->load('category', 'stockMutations.user', 'stockMutations.supplier');
        $mutasiTerbaru = $product->stockMutations()->latest()->limit(10)->get();

        return view('admin.products.show', compact('product', 'mutasiTerbaru'));
    }

    /**
     * Form edit produk.
     */
    public function edit(Product $product)
    {
        $colNamaKategori = 'nama_kategori';
        $categories = Category::active()->orderBy($colNamaKategori)->get();
        $satuan = $this->getSatuanList();

        return view('admin.products.edit', compact('product', 'categories', 'satuan'));
    }

    /**
     * Update data produk.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id'  => 'nullable|exists:categories,id',
            'sku'          => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product->id)],
            'nama_produk'  => 'required|string|max:200',
            'deskripsi'    => 'nullable|string',
            'satuan'       => 'required|string|max:30',
            'modal_hpp'    => 'required|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'foto'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'    => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        // Upload foto baru
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($product->foto) {
                Storage::disk('public')->delete($product->foto);
            }
            $validated['foto'] = $request->file('foto')->store('products', 'public');
        } else {
            // Jangan hapus foto lama jika tidak ada upload baru
            unset($validated['foto']);
        }

        $product->update($validated);

        CacheService::forgetProductsByCategory((int) $product->category_id);

        \App\Events\CatalogUpdated::dispatch();

        ActivityLog::log('Edit Produk', "Admin mengubah produk: [{$product->nama_produk}]", $product);

        return redirect()->route('admin.products.show', $product)
            ->with('success', "Produk <strong>{$product->nama_produk}</strong> berhasil diperbarui!");
    }

    /**
     * Soft delete produk (data tetap di DB untuk keamanan histori transaksi).
     */
    public function destroy(Product $product)
    {
        CacheService::forgetProductsByCategory((int) $product->category_id);
        
        \App\Events\CatalogUpdated::dispatch();

        ActivityLog::log('Hapus Produk', "Admin menghapus produk: [{$product->nama_produk}]", $product);

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', "Produk <strong>{$product->nama_produk}</strong> berhasil dihapus (dapat dipulihkan).");
    }

    /**
     * Restore produk yang di-soft delete.
     */
    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        CacheService::forgetProductsByCategory((int) $product->category_id);

        \App\Events\CatalogUpdated::dispatch();

        ActivityLog::log('Restore Produk', "Admin memulihkan produk: [{$product->nama_produk}]", $product);

        return redirect()->route('admin.products.show', $product)
            ->with('success', "Produk <strong>{$product->nama_produk}</strong> berhasil dipulihkan!");
    }

    /**
     * Hapus foto produk saja (tanpa hapus produk).
     */
    public function destroyFoto(Product $product)
    {
        if ($product->foto) {
            Storage::disk('public')->delete($product->foto);
            $product->update(['foto' => null]);
        }

        return back()->with('success', 'Foto produk berhasil dihapus.');
    }

    /**
     * === FASE 3.2: Generate SKU/Barcode unik 12-digit ===
     * Dipanggil via AJAX dari form produk.
     * Format: Timestamp-based untuk memastikan keunikan.
     */
    public function generateSku(Request $request)
    {
        $colSku = 'sku';
        do {
            // Generate 12 digit numerik: 4 digit random prefix + 8 digit timestamp
            $sku = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) . substr(time(), -8);
        } while (Product::withTrashed()->where($colSku, $sku)->exists());

        return response()->json(['sku' => $sku]);
    }

    /**
     * Daftar satuan barang yang umum dipakai.
     */
    private function getSatuanList(): array
    {
        return [
            'pcs', 'unit', 'buah', 'biji', 'lusin',
            'kodi', 'kg', 'gram', 'ons', 'liter',
            'ml', 'botol', 'dus', 'karton', 'pack',
            'bungkus', 'sachet', 'lembar', 'rim', 'roll',
            'meter', 'cm', 'pasang', 'set', 'box',
        ];
    }
}
