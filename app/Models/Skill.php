<?php

namespace App\Models;

use App\Enums\CamperCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    protected $guarded = ['id'];

    // ── Relationships ────────────────────────────────────────────────────────

    public function registrations(): HasMany
    {
        return $this->hasMany(CamperSkillRegistration::class);
    }

    // ── Computed helpers ─────────────────────────────────────────────────────

    public function registeredCount(): int
    {
        return $this->registrations()->count();
    }

    public function remainingSlots(): int
    {
        return max(0, $this->maximum_attendees - $this->registeredCount());
    }

    public function isFull(): bool
    {
        return $this->remainingSlots() <= 0;
    }

    public function isGeneral(): bool
    {
        return $this->category === null;
    }

    public function categoryLabel(): string
    {
        return match ($this->category) {
            'adventurer'   => 'Adventurers',
            'pathfinder'   => 'Pathfinders',
            'senior_youth' => 'Senior Youth',
            null           => 'General',
            default        => ucfirst($this->category),
        };
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /** Skills a specific camper is eligible for, with available slots. */
    public function scopeEligibleFor($query, Camper $camper): void
    {
        $catValue = $camper->category instanceof CamperCategory
            ? $camper->category->value
            : (string) $camper->category;

        $query
            ->where('status', 'active')
            ->where(function ($q) use ($catValue, $camper) {
                // General: no category restriction
                $q->whereNull('category')
                    // Category-specific (with optional rank filter)
                    ->orWhere(function ($q2) use ($catValue, $camper) {
                        $q2->where('category', $catValue)
                            ->where(function ($q3) use ($camper) {
                                $q3->whereNull('club_rank')
                                    ->orWhere('club_rank', $camper->club_rank);
                            });
                    });
            })
            // Only skills with remaining capacity
            ->where('maximum_attendees', '>', function ($sub) {
                $sub->selectRaw('COUNT(*)')
                    ->from('camper_skill_registrations')
                    ->whereColumn('skill_id', 'skills.id');
            });
    }

    public function scopeActive($query): void
    {
        $query->where('status', 'active');
    }
}
