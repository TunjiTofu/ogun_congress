<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampMedia extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_featured' => 'boolean'];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function camper(): BelongsTo
    {
        return $this->belongsTo(Camper::class);
    }
}
