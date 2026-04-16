<?php

namespace App\Models;

use App\Enums\CodeStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RegistrationCode extends Model
{
    protected $fillable = [
        'code',
        'payment_type',
        'status',
        'prefill_name',
        'prefill_phone',
        'amount_paid',
        'paystack_reference',
        'offline_payment_id',
        'activated_at',
        'expires_at',
        'claimed_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'payment_type'  => PaymentType::class,
            'status'        => CodeStatus::class,
            'amount_paid'   => 'decimal:2',
            'activated_at'  => 'datetime',
            'expires_at'    => 'datetime',
            'claimed_at'    => 'datetime',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function camper(): HasOne
    {
        return $this->hasOne(Camper::class);
    }

    public function offlinePayment(): BelongsTo
    {
        return $this->belongsTo(OfflinePayment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', CodeStatus::ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', CodeStatus::PENDING);
    }

    public function scopeClaimed($query)
    {
        return $query->where('status', CodeStatus::CLAIMED);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === CodeStatus::ACTIVE;
    }

    public function isClaimed(): bool
    {
        return $this->status === CodeStatus::CLAIMED;
    }

    public function isPending(): bool
    {
        return $this->status === CodeStatus::PENDING;
    }

    public function isExpired(): bool
    {
        return $this->status === CodeStatus::EXPIRED;
    }

    public function isOnline(): bool
    {
        return $this->payment_type === PaymentType::ONLINE;
    }
}
