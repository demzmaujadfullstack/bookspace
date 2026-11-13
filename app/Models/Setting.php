<?php

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    private const CACHE_KEY = 'settings:all';

    /**
     * Semua pengaturan sebagai key => value, dibaca dari cache.
     *
     * @return array<string, string|null>
     */
    public static function values(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(5), fn () => static::pluck('value', 'key')->all());
    }

    public static function value(string $key, ?string $default = null): ?string
    {
        return static::values()[$key] ?? $default;
    }

    public static function put(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /** @return void */
    protected static function booted()
    {
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }
}
