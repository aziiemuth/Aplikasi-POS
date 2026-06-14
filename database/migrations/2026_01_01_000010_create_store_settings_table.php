<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed default values
        $defaults = [
            ['key' => 'nama_toko',      'value' => 'Aplikasi POS'],
            ['key' => 'alamat',         'value' => 'Jl. Contoh No. 1'],
            ['key' => 'kontak',         'value' => '0812-3456-7890'],
            ['key' => 'footer_struk',   'value' => 'Terima kasih sudah berbelanja!'],
            ['key' => 'logo',           'value' => null],
            ['key' => 'kota',           'value' => 'Jakarta'],
            ['key' => 'website',        'value' => ''],
            ['key' => 'pajak_default',  'value' => '0'],
        ];

        foreach ($defaults as $row) {
            DB::table('store_settings')->insert(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
