<?php

namespace App\Models;

use App\Enums\CamperCategory;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    use HasFactory;

    protected $guarded = ['id'];

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
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->useDisk('public'); // Public disk so photos are accessible via URL
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(400)
            ->performOnCollections('photo')
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

    public function getAgeAttribute(): ?int
    {
        if (! $this->date_of_birth) return null;
        return Carbon::parse($this->date_of_birth)->age;
    }

    public function requiresConsentForm(): bool
    {
        // If DOB is unknown, use category — Adventurers and Pathfinders are always under 18
        if (! $this->date_of_birth) {
            return in_array($this->category, [
                \App\Enums\CamperCategory::ADVENTURER,
                \App\Enums\CamperCategory::PATHFINDER,
            ]);
        }
        return $this->age < 18;
    }

    public function isCheckedIn(): bool
    {
        return $this->checkinEvents()
            ->where('event_type', \App\Enums\CheckinEventType::CHECK_IN)
            ->exists();
    }

    /**
     * Add a computed is_checked_in boolean based on the last check-in event.
     */
    public function scopeWithLastCheckinStatus(Builder $query): void
    {
        $query->addSelect([
            'is_checked_in' => \App\Models\CheckinEvent::select('event_type')
                ->whereColumn('camper_id', 'campers.id')
                ->latest('occurred_at')
                ->limit(1),
        ])->selectRaw("
            CASE
                WHEN (
                    SELECT event_type FROM checkin_events
                    WHERE camper_id = campers.id
                    ORDER BY occurred_at DESC LIMIT 1
                ) = 'check_in' THEN 1
                ELSE 0
            END as is_checked_in
        ");
    }
}
