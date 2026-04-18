<?php

namespace App\Models;

use App\Enums\CamperCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulkRegistrationEntry extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'category' => CamperCategory::class,
            'fee'      => 'decimal:2',
        ];
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
