<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel stock_mutations: Kartu Stok / Riwayat Mutasi Stok
     * Setiap perubahan stok (masuk/keluar) tercatat di sini
     */
    public function up(): void
    {
        Schema::create('stock_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->restrictOnDelete();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete()
                  ->comment('User yang melakukan mutasi stok');
            $table->foreignId('order_id')
                  ->nullable()
                  ->constrained('orders')
                  ->nullOnDelete()
                  ->comment('Referensi ke order jika stok keluar karena terjual');
            $table->foreignId('supplier_id')
                  ->nullable()
                  ->constrained('suppliers')
                  ->nullOnDelete()
                  ->comment('Referensi supplier jika stok masuk dari pembelian');
            $table->enum('tipe', ['masuk', 'keluar'])->comment('Stok masuk atau keluar');
            $table->integer('jumlah')->comment('Kuantitas yang berubah (selalu positif)');
            $table->integer('stok_sebelum')->comment('Stok sebelum mutasi (untuk audit)');
            $table->integer('stok_sesudah')->comment('Stok setelah mutasi (untuk audit)');
            $table->decimal('harga_beli', 15, 2)->nullable()->comment('Harga beli per unit saat stok masuk');
            $table->string('keterangan')->nullable()->comment('Catatan tambahan (misal: Pembelian dari Supplier X)');
            $table->timestamps();

            // Index untuk kartu stok per produk
            $table->index(['product_id', 'tipe', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_mutations');
    }
};
