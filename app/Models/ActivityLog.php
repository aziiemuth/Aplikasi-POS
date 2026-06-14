<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'aksi',
        'deskripsi',
        'model_type',
        'model_id',
        'ip_address',
        'user_agent',
    ];

    /**
     * Flag untuk menandai apakah ada manual log yang ditulis dalam request ini.
     */
    public static bool $hasLoggedManual = false;

    /**
     * Relasi: Log dilakukan oleh satu user (nullable untuk log sistem).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Helper static: catat aktivitas dengan mudah.
     *
     * @param string $aksi Nama aksi (contoh: 'Login', 'Checkout', 'Tambah Produk')
     * @param string|null $deskripsi Deskripsi detail
     * @param Model|null $model Model yang terdampak
     */
    public static function log(string $aksi, ?string $deskripsi = null, ?Model $model = null): void
    {
        $request = request();

        self::create([
            'user_id'    => auth()->id(),
            'aksi'       => $aksi,
            'deskripsi'  => $deskripsi,
            'model_type' => $model ? get_class($model) : null,
            'model_id'   => $model?->getKey(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Tandai bahwa manual log telah ditulis pada request ini
        self::$hasLoggedManual = true;
    }
}
