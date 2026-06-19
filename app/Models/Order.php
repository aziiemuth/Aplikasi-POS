<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Order
 *
 * @property int $id
 * @property int $user_id
 * @property string $nomor_order
 * @property string $nama_customer
 * @property float $total_sebelum_diskon
 * @property float $diskon_global
 * @property float $pajak_ppn
 * @property float $total_pembayaran
 * @property string $metode_pembayaran
 * @property float $jumlah_bayar
 * @property float $kembalian
 * @property string $status
 * @property string|null $catatan
 * @property float $laba_kotor
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nomor_order',
        'nama_customer',
        'total_sebelum_diskon',
        'diskon_global',
        'pajak_ppn',
        'total_pembayaran',
        'metode_pembayaran',
        'jumlah_bayar',
        'kembalian',
        'status',
        'catatan',
    ];

    protected $casts = [
        'total_sebelum_diskon' => 'decimal:2',
        'diskon_global'        => 'decimal:2',
        'pajak_ppn'            => 'decimal:2',
        'total_pembayaran'     => 'decimal:2',
        'jumlah_bayar'         => 'decimal:2',
        'kembalian'            => 'decimal:2',
    ];

    /**
     * Relasi: Order dilayani oleh satu kasir (user).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Satu order memiliki banyak item.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi: Satu order bisa ada di banyak stock_mutations (stok keluar).
     */
    public function stockMutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }

    /**
     * Scope: hanya order yang sudah lunas.
     */
    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    /**
     * Scope: order yang masih open bill.
     */
    public function scopeOpenBill($query)
    {
        return $query->where('status', 'open_bill');
    }

    /**
     * Scope: order pada tanggal tertentu.
     */
    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    /**
     * Scope: order sejak tanggal tertentu.
     */
    public function scopeSinceDate($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    /**
     * Scope: order terakhir dengan prefix nomor order tertentu.
     */
    public function scopeLatestByNumber($query, $prefix)
    {
        return $query->where('nomor_order', 'like', $prefix . '%')
                     ->orderByDesc('id');
    }

    /**
     * Scope: filter order milik user/kasir tertentu.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Accessor: total laba kotor dari seluruh item.
     * Laba = (harga_jual_snapshot - diskon_item - hpp_snapshot) * jumlah
     * Dikurangi diskon_global
     */
    public function getLabaKotorAttribute(): float
    {
        $labaItems = $this->items->sum(function ($item) {
            return ($item->harga_jual_snapshot - $item->diskon_item - $item->hpp_snapshot) * $item->jumlah;
        });

        return $labaItems - $this->diskon_global;
    }

    /**
     * Generate nomor order unik format: INV-YYYYMMDD-XXXX
     */
    public static function generateNomorOrder(): string
    {
        $prefix = 'INV-' . date('Ymd') . '-';
        $lastOrder = Order::latestByNumber($prefix)->first();

        $lastNumber = $lastOrder ? (int) substr($lastOrder->nomor_order, -4) : 0;

        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}
