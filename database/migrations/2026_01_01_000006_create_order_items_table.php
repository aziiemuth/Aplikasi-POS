<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel order_items: Detail Item Per Transaksi
     * KRUSIAL: Menyimpan SNAPSHOT harga_jual dan HPP saat transaksi terjadi
     * agar kalkulasi laba tidak berubah meskipun harga master produk diedit kemudian
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->restrictOnDelete();
            $table->string('nama_produk_snapshot', 200)->comment('Nama produk saat transaksi terjadi');
            $table->decimal('harga_jual_snapshot', 15, 2)->comment('Harga jual saat checkout - JANGAN diubah');
            $table->decimal('hpp_snapshot', 15, 2)->comment('HPP/Modal saat checkout - untuk kalkulasi laba');
            $table->decimal('diskon_item', 15, 2)->default(0)->comment('Diskon per item dalam rupiah');
            $table->integer('jumlah')->default(1);
            $table->decimal('total_harga_item', 15, 2)->comment('(harga_jual - diskon) * jumlah');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
