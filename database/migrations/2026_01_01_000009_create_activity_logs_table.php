<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel activity_logs: Rekam Jejak Aktivitas Semua User (Audit Trail)
     * Digunakan Admin/Owner untuk memantau aktivitas kasir
     */
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User yang melakukan aksi (null jika sistem)');
            $table->string('aksi', 100)->comment('Login, Logout, Tambah Produk, Checkout, dll');
            $table->text('deskripsi')->nullable()->comment('Detail lengkap aksi yang dilakukan');
            $table->string('model_type', 100)->nullable()->comment('Nama model yang dipengaruhi (App\Models\Product, dll)');
            $table->unsignedBigInteger('model_id')->nullable()->comment('ID record yang dipengaruhi');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Index untuk query log per user dan aksi
            $table->index(['user_id', 'aksi', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
