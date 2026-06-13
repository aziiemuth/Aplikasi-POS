<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int|null $category_id
 * @property string $sku
 * @property string $nama_produk
 * @property string|null $deskripsi
 * @property string $satuan
 * @property float $modal_hpp
 * @property float $harga_jual
 * @property int $stok_saat_ini
 * @property int $stok_minimum
 * @property string|null $foto
 * @property bool $is_active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'sku',
        'nama_produk',
        'deskripsi',
        'satuan',
        'modal_hpp',
        'harga_jual',
        'stok_saat_ini',
        'stok_minimum',
        'foto',
        'is_active',
    ];

    protected $casts = [
        'modal_hpp'    => 'decimal:2',
        'harga_jual'   => 'decimal:2',
        'is_active'    => 'boolean',
        'stok_saat_ini'=> 'integer',
        'stok_minimum' => 'integer',
    ];

    /**
     * Relasi: Produk milik satu kategori.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi: Satu produk bisa ada di banyak mutasi stok.
     */
    public function stockMutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }

    /**
     * Relasi: Produk bisa ada di banyak item pesanan.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi: Produk bisa ada di keranjang.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Scope: hanya produk aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: produk dengan stok rendah (di bawah minimum).
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stok_saat_ini', '<=', 'stok_minimum');
    }

    /**
     * Accessor: apakah stok tersedia untuk dijual.
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->is_active && $this->stok_saat_ini > 0;
    }

    /**
     * Accessor: laba per unit (harga jual - HPP).
     */
    public function getLabaPerUnitAttribute(): float
    {
        return $this->harga_jual - $this->modal_hpp;
    }
}
