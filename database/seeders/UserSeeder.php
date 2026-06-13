<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed akun admin pertama dan beberapa kasir demo.
     */
    public function run(): void
    {
        // === AKUN ADMIN UTAMA ===
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name'              => 'Administrator',
                'username'          => 'admin',
                'email'             => 'admin@pos.local',
                'phone'             => '081234567890',
                'role'              => 'admin',
                'is_active'         => true,
                'password'          => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // === AKUN OWNER/SUPERADMIN ===
        User::updateOrCreate(
            ['username' => 'owner'],
            [
                'name'              => 'Owner Toko',
                'username'          => 'owner',
                'email'             => 'owner@pos.local',
                'phone'             => '081234567891',
                'role'              => 'admin',
                'is_active'         => true,
                'password'          => Hash::make('owner123'),
                'email_verified_at' => now(),
            ]
        );

        // === AKUN KASIR DEMO ===
        User::updateOrCreate(
            ['username' => 'kasir1'],
            [
                'name'              => 'Kasir Satu',
                'username'          => 'kasir1',
                'email'             => 'kasir1@pos.local',
                'phone'             => '081234567892',
                'role'              => 'kasir',
                'is_active'         => true,
                'password'          => Hash::make('kasir123'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['username' => 'kasir2'],
            [
                'name'              => 'Kasir Dua',
                'username'          => 'kasir2',
                'email'             => 'kasir2@pos.local',
                'phone'             => '081234567893',
                'role'              => 'kasir',
                'is_active'         => true,
                'password'          => Hash::make('kasir123'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ UserSeeder berhasil! Akun:');
        $this->command->table(
            ['Username', 'Password', 'Role'],
            [
                ['admin',  'admin123',  'admin'],
                ['owner',  'owner123',  'admin'],
                ['kasir1', 'kasir123',  'kasir'],
                ['kasir2', 'kasir123',  'kasir'],
            ]
        );
    }
}
