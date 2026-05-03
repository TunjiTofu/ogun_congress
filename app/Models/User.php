<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
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

    public function isChurchCoordinator(): bool
    {
        return $this->hasRole('church_coordinator');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function canAccessFilament(): bool
    {
        return $this->is_active;
    }

    // Add this instead:
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && $this->hasRole([
                'super_admin',
                'accountant',
                'secretariat',
                'security',
                'church_coordinator',
            ]);
    }
}
