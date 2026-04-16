<?php

namespace App\Enums;

enum ContactType: string
{
    case PARENT_GUARDIAN   = 'parent_guardian';
    case EMERGENCY_CONTACT = 'emergency_contact';

    public function label(): string
    {
        return match($this) {
            self::PARENT_GUARDIAN   => 'Parent / Guardian',
            self::EMERGENCY_CONTACT => 'Emergency Contact',
        };
    }
}
