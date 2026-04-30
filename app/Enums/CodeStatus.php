<?php

namespace App\Enums;

enum CodeStatus: string
{
    case PENDING = 'PENDING';
    case ACTIVE  = 'ACTIVE';
    case CLAIMED = 'CLAIMED';
    case EXPIRED = 'EXPIRED';
    case VOID    = 'VOID';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending Payment',
            self::ACTIVE  => 'Active',
            self::CLAIMED => 'Claimed',
            self::EXPIRED => 'Expired',
            self::VOID    => 'Void',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::ACTIVE  => 'success',
            self::CLAIMED => 'info',
            self::EXPIRED => 'gray',
            self::VOID    => 'danger',
        };
    }

    /** Statuses that cannot transition to ACTIVE */
    public function isTerminal(): bool
    {
        return in_array($this, [self::CLAIMED, self::VOID]);
    }

    public function userMessage(): string
    {
        return match($this) {
            self::PENDING => 'Your payment has not yet been confirmed. Please wait a few minutes and try again, or contact the accountant.',
            self::CLAIMED => 'This code has already been used to complete a registration. If this is an error, please contact the secretariat.',
            self::EXPIRED => 'This code has expired. Please contact the accountant to have it reactivated.',
            self::VOID    => 'This code has been cancelled. Please contact the secretariat for assistance.',
            self::ACTIVE  => 'Code is valid.',
        };
    }
}
