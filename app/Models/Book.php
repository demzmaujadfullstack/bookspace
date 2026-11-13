<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'category_id', 'title', 'slug', 'author', 'publisher', 'isbn', 'year',
    'description', 'price', 'discount_percent', 'stock', 'cover', 'is_featured',
])]
class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    public const STOCK_LOW_THRESHOLD = 5;

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

    /** Harga akhir setelah diskon persen, dibulatkan ke ratusan rupiah. */
    public function finalPrice(): int
    {
        return (int) round($this->price * (1 - $this->discount_percent / 100), -2);
    }

    public function hasDiscount(): bool
    {
        return $this->discount_percent > 0 && $this->finalPrice() < $this->price;
    }

    public function coverUrl(): string
    {
        if ($this->cover && Storage::disk('public')->exists($this->cover)) {
            return asset('storage/'.$this->cover);
        }

        return asset('images/cover-placeholder.svg');
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
        return round((float) $this->reviews()->avg('rating'), 1);
    }

    /** Eksemplar terjual pada transaksi yang pembayarannya sudah dikonfirmasi. */
    public function soldCount(): int
    {
        return (int) $this->orderItems()
            ->whereHas('order', fn (Builder $q) => $q->whereIn('status', Order::STOCK_REDUCED_STATUSES))
            ->sum('quantity');
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
