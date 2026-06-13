<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_supplier',
        'kontak',
        'email',
        'alamat',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi: Satu supplier bisa ada di banyak stock_mutations (pembelian).
     */
    public function stockMutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
