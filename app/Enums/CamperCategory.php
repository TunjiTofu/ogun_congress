<?php

namespace App\Enums;

// Enums
enum CamperCategory: string
{
    case ADVENTURER   = 'adventurer';
    case PATHFINDER   = 'pathfinder';
    case SENIOR_YOUTH = 'senior_youth';

    public function label(): string
    {
        return match($this) {
            self::ADVENTURER   => 'Adventurer',
            self::PATHFINDER   => 'Pathfinder',
            self::SENIOR_YOUTH => 'Senior Youth',
        };
    }

    public function ageRange(): string
    {
        return match($this) {
            self::ADVENTURER   => '6–9',
            self::PATHFINDER   => '10–15',
            self::SENIOR_YOUTH => '16+',
        };
    }

    public function requiresParentalConsent(): bool
    {
        return in_array($this, [self::ADVENTURER, self::PATHFINDER]);
    }

    public function color(): string
    {
        return match($this) {
            self::ADVENTURER   => 'info',
            self::PATHFINDER   => 'success',
            self::SENIOR_YOUTH => 'warning',
        };
    }

    public static function fromAge(int $age): self
    {
        return match(true) {
            $age >= 6  && $age <= 9  => self::ADVENTURER,
            $age >= 10 && $age <= 15 => self::PATHFINDER,
            $age >= 16               => self::SENIOR_YOUTH,
            default => throw new \InvalidArgumentException(
                "Age {$age} does not meet camp requirements. Minimum age is 6."
            ),
        };
    }
}
