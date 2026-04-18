<?php

namespace App\Models;

use App\Enums\CamperCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BulkRegistrationBatch extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'expected_total' => 'decimal:2',
            'amount_paid'    => 'decimal:2',
            'deposit_date'   => 'date',
            'confirmed_at'   => 'datetime',
        ];
    }

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(BulkRegistrationEntry::class, 'batch_id');
    }

    public function isDraft(): bool         { return $this->status === 'draft'; }
    public function isPendingPayment(): bool { return $this->status === 'pending_payment'; }
    public function isConfirmed(): bool      { return $this->status === 'confirmed'; }

    /**
     * Recalculate the expected total from entry fees.
     */
    public function recalculateTotal(): void
    {
        $this->update([
            'expected_total' => $this->entries()->sum('fee'),
        ]);
    }
}
