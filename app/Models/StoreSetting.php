<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class StoreSetting
 *
 * @property int    $id
 * @property string $key
 * @property string|null $value
 */
class StoreSetting extends Model
{
    protected $table = 'store_settings';

    protected $fillable = ['key', 'value'];

    /**
     * Ambil nilai setting berdasarkan key.
     * Mengembalikan $default jika key tidak ditemukan.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set atau update nilai setting.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Ambil semua setting sebagai array asosiatif key => value.
     */
    public static function all_settings(): array
    {
        return static::all()->pluck('value', 'key')->toArray();
    }
}
