<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'role',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // =========================================================
    // RELASI
    // =========================================================

    /**
     * Semua order/transaksi yang dilayani kasir ini.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Semua mutasi stok yang dilakukan user ini.
     */
    public function stockMutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }

    /**
     * Keranjang belanja aktif kasir ini.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Log aktivitas user ini.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // =========================================================
    // HELPER ROLE
    // =========================================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    // =========================================================
    // SCOPE
    // =========================================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeKasir($query)
    {
        return $query->where('role', 'kasir');
    }
}
