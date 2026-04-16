<?php

namespace App\Enums;

enum CheckinEventType: string
{
    case CHECK_IN             = 'check_in';
    case CHECK_OUT            = 'check_out';
    case PROGRAMME_ATTENDANCE = 'programme_attendance';

    public function label(): string
    {
        return match($this) {
            self::CHECK_IN             => 'Check In',
            self::CHECK_OUT            => 'Check Out',
            self::PROGRAMME_ATTENDANCE => 'Programme Attendance',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::CHECK_IN             => 'success',
            self::CHECK_OUT            => 'warning',
            self::PROGRAMME_ATTENDANCE => 'info',
        };
    }
}
