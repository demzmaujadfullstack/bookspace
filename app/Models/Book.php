<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'category_id', 'title', 'slug', 'author', 'publisher', 'isbn', 'year',
    'description', 'price', 'discount_percent', 'stock', 'cover', 'is_featured',
])]
class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    public const STOCK_LOW_THRESHOLD = 5;

    /** @return void */
    protected static function booted()
    {
        // Badge jumlah buku per kategori di menu ikut berubah saat buku
        // disimpan, dihapus, atau dipindah kategori.
        static::saved(fn () => Category::flushNavCache());
        static::deleted(fn () => Category::flushNavCache());
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    /** Data yang dibutuhkan komponen x-book-card, supaya tidak ada query per kartu. */
    public function scopeForCards(Builder $query): Builder
    {
        return $query
            ->with('category')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews');
    }

    /** Jumlah eksemplar dari transaksi yang sudah dikonfirmasi, dimuat dalam satu query. */
    public function scopeWithSoldCount(Builder $query): Builder
    {
        return $query->withCount([
            'orderItems as sold_count' => fn (Builder $q) => $q->whereHas('order', fn (Builder $o) => $o->whereIn('status', Order::STOCK_REDUCED_STATUSES)),
        ]);
    }

    /** Harga akhir setelah diskon persen, dibulatkan ke ratusan rupiah. */
    public function finalPrice(): int
    {
        return (int) round($this->price * (1 - $this->discount_percent / 100), -2);
    }

    public function hasDiscount(): bool
    {
        return $this->discount_percent > 0 && $this->finalPrice() < $this->price;
    }

    /**
     * Sampul dipercaya dari kolom `cover`; bila filenya hilang, atribut
     * onerror pada <img> yang mengganti dengan placeholder. Ini menghindarkan
     * satu cek filesystem per buku saat menampilkan daftar.
     */
    public function coverUrl(): string
    {
        return $this->cover
            ? asset('storage/'.$this->cover)
            : asset('images/cover-placeholder.svg');
    }

    public function stockStatus(): string
    {
        return match (true) {
            $this->stock === 0 => 'Habis',
            $this->stock <= self::STOCK_LOW_THRESHOLD => 'Stok menipis',
            default => 'Tersedia',
        };
    }

    public function averageRating(): float
    {
        // NULL artinya buku ini belum dinilai, bukan datanya belum dimuat —
        // jadi cek keberadaan kolomnya, bukan kekosongan nilainya.
        $avg = array_key_exists('reviews_avg_rating', $this->attributes)
            ? $this->reviews_avg_rating
            : $this->reviews()->avg('rating');

        return round((float) $avg, 1);
    }

    /** Eksemplar terjual pada transaksi yang pembayarannya sudah dikonfirmasi. */
    public function soldCount(): int
    {
        return (int) ($this->sold_count ?? $this->orderItems()
            ->whereHas('order', fn (Builder $q) => $q->whereIn('status', Order::STOCK_REDUCED_STATUSES))
            ->sum('quantity'));
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'discount_percent' => 'integer',
            'stock' => 'integer',
            'year' => 'integer',
            'is_featured' => 'boolean',
        ];
    }
}
