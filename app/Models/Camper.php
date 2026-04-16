<?php

namespace App\Models;

use App\Enums\CamperCategory;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Camper extends Model implements HasMedia
{
    use InteractsWithMedia;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'registration_code_id',
        'camper_number',
        'full_name',
        'phone',
        'date_of_birth',
        'gender',
        'category',
        'home_address',
        'church_id',
        'ministry',
        'club_rank',
        'volunteer_role',
        'photo_path',
        'badge_color',
        'id_card_path',
        'consent_form_path',
        'consent_collected',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth'     => 'date',
            'gender'            => Gender::class,
            'category'          => CamperCategory::class,
            'consent_collected' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'full_name', 'phone', 'date_of_birth', 'gender',
                'category', 'church_id', 'ministry', 'consent_collected',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ── Media Library ─────────────────────────────────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')
             ->singleFile()
             ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
             ->width(400)
             ->height(400)
             ->nonQueued();
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function registrationCode(): BelongsTo
    {
        return $this->belongsTo(RegistrationCode::class);
    }

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function health(): HasOne
    {
        return $this->hasOne(CamperHealth::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(CamperContact::class);
    }

    public function checkinEvents(): HasMany
    {
        return $this->hasMany(CheckinEvent::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeAdventurers($query)
    {
        return $query->where('category', CamperCategory::ADVENTURER);
    }

    public function scopePathfinders($query)
    {
        return $query->where('category', CamperCategory::PATHFINDER);
    }

    public function scopeSeniorYouth($query)
    {
        return $query->where('category', CamperCategory::SENIOR_YOUTH);
    }

    public function scopeConsentOutstanding($query)
    {
        return $query->where('consent_collected', false)
                     ->whereIn('category', [
                         CamperCategory::ADVENTURER,
                         CamperCategory::PATHFINDER,
                     ]);
    }

    // ── Computed / Helpers ────────────────────────────────────────────────────

    public function getAgeAttribute(): int
    {
        return Carbon::parse($this->date_of_birth)->age;
    }

    public function requiresConsentForm(): bool
    {
        return $this->age < 18;
    }

    public function isCheckedIn(): bool
    {
        return $this->checkinEvents()
                    ->where('event_type', \App\Enums\CheckinEventType::CHECK_IN)
                    ->exists();
    }
}
