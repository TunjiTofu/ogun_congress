<?php

namespace App\Models;

use App\Enums\CamperCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Skill extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'target_categories' => 'array',
    ];

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
        return empty($this->target_categories) && $this->category === null;
    }

    public function categoryLabel(): string
    {
        // Use target_categories first (new multi-category field)
        if (! empty($this->target_categories)) {
            $labels = array_map(fn ($c) => match ($c) {
                'adventurer'   => 'Adventurers',
                'pathfinder'   => 'Pathfinders',
                'senior_youth' => 'Senior Youth',
                default        => ucfirst($c),
            }, $this->target_categories);
            return implode(' & ', $labels);
        }

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
                // New: target_categories JSON array contains camper's category
                $q->where(function ($q2) use ($catValue) {
                    $q2->whereNotNull('target_categories')
                        ->whereJsonContains('target_categories', $catValue);
                })
                    // Legacy fallback: use the old single category column
                    ->orWhere(function ($q2) use ($catValue, $camper) {
                        $q2->whereNull('target_categories')
                            ->where(function ($q3) use ($catValue, $camper) {
                                $q3->whereNull('category')
                                    ->orWhere(function ($q4) use ($catValue, $camper) {
                                        $q4->where('category', $catValue)
                                            ->where(function ($q5) use ($camper) {
                                                $q5->whereNull('club_rank')
                                                    ->orWhere('club_rank', $camper->club_rank);
                                            });
                                    });
                            });
                    });
            })
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
