<?php

namespace App\Models;

use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasFactory;

    private const CACHE_KEY = 'settings:all';

    /** @var array<string, string|null> */
    private static array $mediaUrls = [];

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
        static::$mediaUrls = [];
    }

    public static function siteName(): string
    {
        return static::value('site_name') ?: 'BOOKSPACE';
    }

    /** URL aset gambar yang disimpan di disk public, null bila kosong atau file-nya hilang. */
    public static function mediaUrl(string $key): ?string
    {
        $path = static::value($key);
        $memo = $key.'|'.($path ?? '');

        if (! array_key_exists($memo, static::$mediaUrls)) {
            $disk = Storage::disk('public');
            static::$mediaUrls[$memo] = filled($path) && $disk->exists($path)
                ? asset('storage/'.$path).'?v='.$disk->lastModified($path)
                : null;
        }

        return static::$mediaUrls[$memo];
    }

    public static function logoUrl(): ?string
    {
        return static::mediaUrl('logo_image');
    }

    /** Favicon mengikuti logo bila admin belum mengunggah favicon sendiri. */
    public static function faviconUrl(): string
    {
        return static::mediaUrl('favicon_image') ?? static::logoUrl() ?? asset('favicon.ico');
    }

    public static function showsWordmark(): bool
    {
        return static::value('logo_show_text', '1') !== '0';
    }

    /** @return void */
    protected static function booted()
    {
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }
}
