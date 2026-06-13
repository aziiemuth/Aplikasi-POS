<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'nama_produk_snapshot',
        'harga_jual_snapshot',
        'hpp_snapshot',
        'diskon_item',
        'jumlah',
        'total_harga_item',
    ];

    protected $casts = [
        'harga_jual_snapshot' => 'decimal:2',
        'hpp_snapshot'        => 'decimal:2',
        'diskon_item'         => 'decimal:2',
        'total_harga_item'    => 'decimal:2',
        'jumlah'              => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * Accessor: Laba per item = (harga_jual - diskon - hpp) * jumlah
     */
    public function getLabaItemAttribute(): float
    {
        return ($this->harga_jual_snapshot - $this->diskon_item - $this->hpp_snapshot) * $this->jumlah;
    }
}
