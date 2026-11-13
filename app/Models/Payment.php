<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'order_id', 'method', 'amount', 'status', 'proof_image',
    'bank_name', 'account_number', 'account_holder', 'note', 'verified_at', 'verified_by',
])]
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    public const MENUNGGU = 'menunggu';

    public const MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';

    public const DITERIMA = 'diterima';

    public const DITOLAK = 'ditolak';

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function methodLabel(): string
    {
        return match ($this->method) {
            'cod' => 'COD (Bayar di Tempat)',
            'transfer' => 'Transfer Bank',
            'qris' => 'QRIS',
            default => ucfirst($this->method),
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::MENUNGGU => 'Menunggu',
            self::MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
            self::DITERIMA => 'Diterima',
            self::DITOLAK => 'Ditolak',
            default => ucfirst($this->status),
        };
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'verified_at' => 'datetime',
        ];
    }
}
