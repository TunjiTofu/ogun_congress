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
            'email_verified_at'    => 'datetime',
            'last_login_at'        => 'datetime',
            'locked_until'         => 'datetime',
            'is_active'            => 'boolean',
            'must_change_password' => 'boolean',
            'password'             => 'hashed',
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

    // ── Filament access ───────────────────────────────────────────────────────

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

    // ── Role helpers ──────────────────────────────────────────────────────────

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

    // ── Password helpers ──────────────────────────────────────────────────────

    /** True when the user is locked out (brute-force protection). */
    public function isLockedOut(): bool
    {
        return $this->locked_until && now()->lt($this->locked_until);
    }

    /** Clear the forced-password-change flag and temp password. */
    public function clearTemporaryPassword(): void
    {
        $this->update([
            'must_change_password' => false,
            'temp_password'        => null,
        ]);
    }

    // ── Scoped camper query ───────────────────────────────────────────────────

    /**
     * Returns a Camper query scoped to this user's access level.
     * Church coordinators see only their church.
     * District coordinators see all churches in their district.
     * All other roles see everything.
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

        return $query; // super_admin, secretariat, camp_director, accountant — see all
    }
}
