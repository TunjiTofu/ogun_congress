<?php

namespace App\Models;

use App\Enums\OfflinePaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class OfflinePayment extends Model
{
    use LogsActivity;

    protected $fillable = [
        'submitted_name',
        'submitted_phone',
        'amount',
        'bank_name',
        'deposit_date',
        'proof_image_path',
        'notes',
        'status',
        'confirmed_by',
        'confirmed_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'status'       => OfflinePaymentStatus::class,
            'amount'       => 'decimal:2',
            'deposit_date' => 'date',
            'confirmed_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'confirmed_by', 'rejection_reason'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function registrationCode(): HasOne
    {
        return $this->hasOne(RegistrationCode::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === OfflinePaymentStatus::PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === OfflinePaymentStatus::CONFIRMED;
    }
}
