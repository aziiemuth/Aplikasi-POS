<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Memodifikasi tabel users bawaan Laravel untuk POS:
     * - Menambah username (unique, untuk login)
     * - Menambah role (admin/kasir)
     * - Menambah phone (opsional)
     * - Menambah is_active (aktif/nonaktif)
     * - Menambah soft deletes (deleted_at)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name');
            $table->string('phone', 20)->nullable()->after('username');
            $table->enum('role', ['admin', 'kasir'])->default('kasir')->after('phone');
            $table->boolean('is_active')->default(true)->after('role');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'phone', 'role', 'is_active', 'deleted_at']);
        });
    }
};
