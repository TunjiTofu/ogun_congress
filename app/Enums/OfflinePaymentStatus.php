<?php

namespace App\Enums;

enum OfflinePaymentStatus: string
{
    case PENDING   = 'pending';
    case CONFIRMED = 'confirmed';
    case REJECTED  = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING   => 'Pending Review',
            self::CONFIRMED => 'Confirmed',
            self::REJECTED  => 'Rejected',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING   => 'warning',
            self::CONFIRMED => 'success',
            self::REJECTED  => 'danger',
        };
    }
}
