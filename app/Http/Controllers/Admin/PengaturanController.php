<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ProdukImport;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * PengaturanController — Fase 8
 * - Pengaturan identitas toko & kustomisasi struk (8.1)
 * - Backup & Restore database (8.2)
 * - Import massal produk via Excel (8.2)
 */
class PengaturanController extends Controller
{
    // ============================================================
    // 8.1 — HALAMAN UTAMA PENGATURAN
    // ============================================================

    public function index()
    {
        $settings = StoreSetting::all_settings();
        $categories = Category::active()->orderBy('nama_kategori')->get();
        return view('admin.pengaturan.index', compact('settings', 'categories'));
    }

    /**
     * Simpan pengaturan identitas toko.
     */
    public function updateIdentitas(Request $request)
    {
        $request->validate([
            'nama_toko'   => 'required|string|max:100',
            'alamat'      => 'nullable|string|max:255',
            'kota'        => 'nullable|string|max:100',
            'kontak'      => 'nullable|string|max:50',
            'website'     => 'nullable|string|max:100',
            'footer_struk'=> 'nullable|string|max:255',
            'pajak_default'=> 'nullable|numeric|min:0|max:100',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $keys = ['nama_toko', 'alamat', 'kota', 'kontak', 'website', 'footer_struk', 'pajak_default'];
        foreach ($keys as $key) {
            StoreSetting::set($key, $request->input($key, ''));
        }

        // Upload logo
        if ($request->hasFile('logo')) {
            $oldLogo = StoreSetting::get('logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $request->file('logo')->store('logo', 'public');
            StoreSetting::set('logo', $path);
        }

        ActivityLog::log('Pengaturan Toko', 'Admin memperbarui identitas toko: ' . $request->nama_toko);

        return redirect()->route('admin.pengaturan.index')
            ->with('success', 'Pengaturan toko berhasil disimpan!');
    }

    /**
     * Hapus logo toko.
     */
    public function deleteLogo()
    {
        $logo = StoreSetting::get('logo');
        if ($logo && Storage::disk('public')->exists($logo)) {
            Storage::disk('public')->delete($logo);
        }
        StoreSetting::set('logo', null);
        return redirect()->route('admin.pengaturan.index')
            ->with('success', 'Logo berhasil dihapus.');
    }

    // ============================================================
    // 8.2a — BACKUP DATABASE
    // ============================================================

    public function backup(): StreamedResponse
    {
        $db     = config('database.connections.mysql.database');
        $user   = config('database.connections.mysql.username');
        $pass   = config('database.connections.mysql.password');
        $host   = config('database.connections.mysql.host');
        $port   = config('database.connections.mysql.port', 3306);

        $filename = 'backup-' . $db . '-' . now()->format('Ymd-His') . '.sql';

        ActivityLog::log('Backup Database', 'Admin mengunduh backup database: ' . $filename);

        return response()->streamDownload(function () use ($db, $user, $pass, $host, $port) {
            $passParam = $pass ? "-p\"{$pass}\"" : '';
            $command = "mysqldump --host={$host} --port={$port} --user=\"{$user}\" {$passParam} --single-transaction --quick --lock-tables=false \"{$db}\"";
            passthru($command);
        }, $filename, [
            'Content-Type'        => 'application/sql',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ============================================================
    // 8.2b — IMPORT PRODUK VIA EXCEL
    // ============================================================

    /**
     * Download template Excel untuk import produk.
     */
    public function importTemplate()
    {
        return Excel::download(new \App\Exports\ProdukTemplateExport(), 'template-import-produk.xlsx');
    }

    /**
     * Proses import file Excel produk.
     */
    public function importProduk(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $import = new ProdukImport();
            Excel::import($import, $request->file('file_excel'));

            $jumlah = $import->getRowCount();
            ActivityLog::log('Import Produk', "Admin berhasil mengimport {$jumlah} produk dari Excel.");

            return redirect()->route('admin.pengaturan.index')
                ->with('success', "Berhasil mengimport <strong>{$jumlah} produk</strong> dari Excel!");
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $pesan = 'Import gagal pada beberapa baris: ';
            foreach (array_slice($failures, 0, 3) as $f) {
                $pesan .= "Baris {$f->row()} ({$f->attribute()}): " . implode(', ', $f->errors()) . ' | ';
            }
            return redirect()->route('admin.pengaturan.index')
                ->with('error', $pesan);
        } catch (\Throwable $e) {
            return redirect()->route('admin.pengaturan.index')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }
}
