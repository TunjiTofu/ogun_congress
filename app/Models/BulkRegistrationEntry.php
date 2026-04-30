<?php

namespace App\Models;

use App\Enums\CamperCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulkRegistrationEntry extends Model
{
    protected $fillable = [
        'batch_id',
        'full_name',
        'phone',
        'category',
        'fee',
        'registration_code_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'category' => CamperCategory::class,
            'fee'      => 'decimal:2',
        ];
    }

    /**
     * Auto-compute fee from category when saving, so the total
     * is always correct even if the form doesn't send the fee field.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $entry) {
            if (! $entry->fee || (float) $entry->fee === 0.0) {
                if ($entry->category instanceof CamperCategory) {
                    $entry->fee = (float) setting("fee_{$entry->category->value}", 5000);
                }
            }
        });

        // After any entry is saved, refresh the batch total
        static::saved(function (self $entry) {
            $batch = $entry->batch()->first();
            if ($batch) {
                $total = static::where('batch_id', $batch->id)->sum('fee');
                $batch->updateQuietly(['expected_total' => $total]);
            }
        });

        static::deleted(function (self $entry) {
            $batch = $entry->batch()->first();
            if ($batch) {
                $total = static::where('batch_id', $batch->id)->sum('fee');
                $batch->updateQuietly(['expected_total' => $total]);
            }
        });
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(BulkRegistrationBatch::class, 'batch_id');
    }

    public function registrationCode(): BelongsTo
    {
        return $this->belongsTo(RegistrationCode::class);
    }
}
