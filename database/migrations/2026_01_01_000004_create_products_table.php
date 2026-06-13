<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel products: Master Data Produk
     * - FK ke categories dan suppliers
     * - Soft Deletes (agar riwayat transaksi tidak error)
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('categories')
                  ->nullOnDelete();
            $table->string('sku', 50)->unique()->comment('Stock Keeping Unit / Barcode');
            $table->string('nama_produk', 200);
            $table->text('deskripsi')->nullable();
            $table->string('satuan', 30)->default('pcs')->comment('pcs, kg, liter, lusin, dll');
            $table->decimal('modal_hpp', 15, 2)->default(0)->comment('Harga Pokok Penjualan rata-rata tertimbang');
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->integer('stok_saat_ini')->default(0);
            $table->integer('stok_minimum')->default(5)->comment('Alert jika stok di bawah nilai ini');
            $table->string('foto')->nullable()->comment('Path ke file foto produk');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Index untuk pencarian cepat
            $table->index(['sku', 'nama_produk', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
