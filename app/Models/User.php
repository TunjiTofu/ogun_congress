<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use LogsActivity;
    use Notifiable;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'is_active'         => 'boolean',
            'password'          => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function offlinePaymentsConfirmed(): HasMany
    {
        return $this->hasMany(OfflinePayment::class, 'confirmed_by');
    }

    public function registrationCodesCreated(): HasMany
    {
        return $this->hasMany(RegistrationCode::class, 'created_by');
    }

    public function church(): BelongsTo
    {
        return $this->belongsTo(Church::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

//    public function canAccessFilament(): bool
//    {
//        return $this->is_active;
//    }

    // Add this instead:
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->hasAnyRole([
                'super_admin',
                'accountant',
                'secretariat',
                'security',
                'church_coordinator',
                'district_coordinator',
                'camp_director',
            ]);
    }

    public function isCampDirector(): bool
    {
        return $this->hasRole('camp_director');
    }

    public function isDistrictCoordinator(): bool
    {
        return $this->hasRole('district_coordinator');
    }

    public function isChurchCoordinator(): bool
    {
        return $this->hasRole('church_coordinator');
    }

    /**
     * Get camper query scoped to this user's access level.
     */
    public function scopedCamperQuery(): Builder
    {
        $query = Camper::query();

        if ($this->isChurchCoordinator() && $this->church_id) {
            return $query->where('church_id', $this->church_id);
        }

        if ($this->isDistrictCoordinator() && $this->district_id) {
            $churchIds = Church::where('district_id', $this->district_id)->pluck('id');
            return $query->whereIn('church_id', $churchIds);
        }

        return $query; // super_admin, secretariat, camp_director — see all
    }
}
