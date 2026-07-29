<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CamperSkillRegistration extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'selected_at' => 'datetime'
    ];

    public function camper(): BelongsTo
    {
        return $this->belongsTo(Camper::class);
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
