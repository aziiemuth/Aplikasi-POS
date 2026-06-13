<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Services\CacheService;
use Illuminate\Http\Request;

/**
 * CategoryController — Fase 3.1: Master Data Kategori
 */
class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('products')
            ->when($request->search, fn($q) => $q->where('nama_kategori', 'like', "%{$request->search}%"))
            ->orderBy('nama_kategori')
            ->paginate(15)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:100|unique:categories,nama_kategori',
            'deskripsi'     => 'nullable|string|max:255',
            'icon'          => 'nullable|string|max:50',
            'is_active'     => 'boolean',
        ], [
            'nama_kategori.unique' => 'Nama kategori sudah ada!',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $category = Category::create($validated);

        // Fase 1.4: Invalidate cache kategori
        CacheService::forgetCategories();
        CacheService::forgetProducts();

        \App\Events\CatalogUpdated::dispatch();

        ActivityLog::log('Tambah Kategori', "Admin menambah kategori: [{$category->nama_kategori}]", $category);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori <strong>{$category->nama_kategori}</strong> berhasil ditambahkan!");
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'nama_kategori' => "required|string|max:100|unique:categories,nama_kategori,{$category->id}",
            'deskripsi'     => 'nullable|string|max:255',
            'icon'          => 'nullable|string|max:50',
            'is_active'     => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $category->update($validated);

        CacheService::forgetCategories();
        CacheService::forgetProducts();

        \App\Events\CatalogUpdated::dispatch();

        ActivityLog::log('Edit Kategori', "Admin mengubah kategori: [{$category->nama_kategori}]", $category);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori <strong>{$category->nama_kategori}</strong> berhasil diperbarui!");
    }

    public function destroy(Category $category)
    {
        // Tidak bisa hapus kategori yang masih punya produk (termasuk yang di-soft delete)
        if ($category->products()->withTrashed()->exists()) {
            return back()->with('error', "Kategori <strong>{$category->nama_kategori}</strong> tidak bisa dihapus karena masih memiliki produk (termasuk produk yang dihapus)!");
        }

        ActivityLog::log('Hapus Kategori', "Admin menghapus kategori: [{$category->nama_kategori}]", $category);
        $category->delete();

        CacheService::forgetCategories();
        CacheService::forgetProducts();

        \App\Events\CatalogUpdated::dispatch();

        return back()->with('success', "Kategori <strong>{$category->nama_kategori}</strong> berhasil dihapus.");
    }

    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => ! $category->is_active]);
        $status = $category->is_active ? 'diaktifkan' : 'dinonaktifkan';

        CacheService::forgetCategories();

        \App\Events\CatalogUpdated::dispatch();

        ActivityLog::log('Toggle Kategori', "Admin {$status} kategori: [{$category->nama_kategori}]", $category);

        return back()->with('success', "Kategori <strong>{$category->nama_kategori}</strong> berhasil {$status}.");
    }
}
