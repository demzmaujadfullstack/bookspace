<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id', 'code', 'status', 'recipient_name', 'phone', 'address', 'total', 'note', 'paid_at',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    public const PENDING = 'pending';

    public const MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';

    public const MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';

    public const DIBAYAR = 'dibayar';

    public const DIPROSES = 'diproses';

    public const DIKIRIM = 'dikirim';

    public const SELESAI = 'selesai';

    public const DIBATALKAN = 'dibatalkan';

    /** Status yang stoknya sudah dikurangi; dasar perhitungan terjual & omzet. */
    public const STOCK_REDUCED_STATUSES = [self::DIBAYAR, self::DIPROSES, self::DIKIRIM, self::SELESAI];

    public const CANCELABLE_STATUSES = [self::PENDING, self::MENUNGGU_PEMBAYARAN, self::MENUNGGU_VERIFIKASI];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::PENDING => 'Pending',
            self::MENUNGGU_PEMBAYARAN => 'Menunggu Pembayaran',
            self::MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
            self::DIBAYAR => 'Dibayar',
            self::DIPROSES => 'Diproses',
            self::DIKIRIM => 'Dikirim',
            self::SELESAI => 'Selesai',
            self::DIBATALKAN => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function canCancel(): bool
    {
        return in_array($this->status, self::CANCELABLE_STATUSES, true);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total' => 'integer',
            'paid_at' => 'datetime',
        ];
    }
}
