<?php

namespace App\Enums;

enum PaymentType: string
{
    case ONLINE  = 'online';
    case OFFLINE = 'offline';

    public function label(): string
    {
        return match($this) {
            self::ONLINE  => 'Online (Paystack)',
            self::OFFLINE => 'Bank Transfer',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::ONLINE  => 'success',
            self::OFFLINE => 'info',
        };
    }
}
