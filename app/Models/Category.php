<?php

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

#[Fillable(['name', 'slug', 'description'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    /** @var Collection<int, static>|null */
    private static ?Collection $navList = null;

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Kategori + jumlah bukunya, dipakai menu navbar dan filter katalog.
     * Hanya dimuat sekali per request.
     *
     * @return Collection<int, static>
     */
    public static function navList(): Collection
    {
        return static::$navList ??= static::withCount('books')->orderBy('name')->get();
    }

    public static function flushNavCache(): void
    {
        static::$navList = null;
    }

    /** @return void */
    protected static function booted()
    {
        static::saved(fn () => static::flushNavCache());
        static::deleted(fn () => static::flushNavCache());
    }
}
