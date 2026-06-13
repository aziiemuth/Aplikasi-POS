<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Supplier;
use Illuminate\Http\Request;

/**
 * SupplierController — Fase 3.1: Master Data Supplier
 * Data Supplier sepenuhnya tersembunyi dari Kasir (route di-protect role:admin)
 */
class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::withCount('stockMutations')
            ->when($request->search, fn($q) => $q->where(function ($q) use ($request) {
                $q->where('nama_supplier', 'like', "%{$request->search}%")
                  ->orWhere('kontak', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->orderBy('nama_supplier')
            ->paginate(15)
            ->withQueryString();

        if ($request->ajax()) {
            return view('admin.suppliers._table', compact('suppliers'))->render();
        }

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:150',
            'kontak'        => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:100',
            'alamat'        => 'nullable|string',
            'keterangan'    => 'nullable|string|max:255',
            'is_active'     => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $supplier = Supplier::create($validated);

        ActivityLog::log('Tambah Supplier', "Admin menambah supplier: [{$supplier->nama_supplier}]", $supplier);

        return redirect()->route('admin.suppliers.index')
            ->with('success', "Supplier <strong>{$supplier->nama_supplier}</strong> berhasil ditambahkan!");
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:150',
            'kontak'        => 'nullable|string|max:30',
            'email'         => 'nullable|email|max:100',
            'alamat'        => 'nullable|string',
            'keterangan'    => 'nullable|string|max:255',
            'is_active'     => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $supplier->update($validated);

        ActivityLog::log('Edit Supplier', "Admin mengubah supplier: [{$supplier->nama_supplier}]", $supplier);

        return redirect()->route('admin.suppliers.index')
            ->with('success', "Supplier <strong>{$supplier->nama_supplier}</strong> berhasil diperbarui!");
    }

    public function destroy(Supplier $supplier)
    {
        // Cek apakah supplier pernah dipakai di mutasi stok
        if ($supplier->stockMutations()->exists()) {
            return back()->with('error', "Supplier <strong>{$supplier->nama_supplier}</strong> tidak bisa dihapus karena sudah pernah digunakan dalam transaksi stok!");
        }

        ActivityLog::log('Hapus Supplier', "Admin menghapus supplier: [{$supplier->nama_supplier}]", $supplier);
        $supplier->delete();

        return back()->with('success', "Supplier <strong>{$supplier->nama_supplier}</strong> berhasil dihapus.");
    }

    public function toggleStatus(Supplier $supplier)
    {
        $supplier->update(['is_active' => ! $supplier->is_active]);
        $status = $supplier->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::log('Toggle Supplier', "Admin {$status} supplier: [{$supplier->nama_supplier}]", $supplier);

        return back()->with('success', "Supplier <strong>{$supplier->nama_supplier}</strong> berhasil {$status}.");
    }
}
