<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel orders: Data Transaksi Penjualan
     * - FK ke users (kasir yang melayani)
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->restrictOnDelete()
                  ->comment('Kasir yang memproses transaksi');
            $table->string('nomor_order', 30)->unique()->comment('Nomor invoice unik (auto-generate)');
            $table->string('nama_customer', 100)->nullable()->default('Umum');
            $table->decimal('total_sebelum_diskon', 15, 2)->default(0);
            $table->decimal('diskon_global', 15, 2)->default(0)->comment('Diskon nominal untuk seluruh transaksi');
            $table->decimal('pajak_ppn', 15, 2)->default(0)->comment('Nominal pajak PPN (bukan persentase)');
            $table->decimal('total_pembayaran', 15, 2)->default(0)->comment('Total akhir yang harus dibayar');
            $table->string('metode_pembayaran', 50)->default('Tunai')->comment('Tunai, Debit BCA, OVO, QRIS, dll');
            $table->decimal('jumlah_bayar', 15, 2)->default(0)->comment('Uang yang diberikan customer');
            $table->decimal('kembalian', 15, 2)->default(0);
            $table->enum('status', ['lunas', 'open_bill', 'void'])->default('lunas');
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Index untuk pencarian cepat per kasir dan status
            $table->index(['user_id', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
