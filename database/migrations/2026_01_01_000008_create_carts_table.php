<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel carts: Keranjang Belanja Sementara per Kasir
     * Data ini bersifat sementara - akan dihapus setelah checkout
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete()
                  ->comment('Kasir pemilik keranjang ini');
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();
            $table->integer('jumlah')->default(1);
            $table->decimal('diskon_item', 15, 2)->default(0)->comment('Diskon per item dalam rupiah');
            $table->timestamps();

            // Setiap kasir hanya boleh punya satu entri per produk
            $table->unique(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
