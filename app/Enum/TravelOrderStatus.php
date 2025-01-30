<?php

namespace App\Enum;

enum TravelOrderStatus : int
{
    case REQUESTED = 1;
    case APPROVED = 2;
    case CANCELLED = 3;

    public static function toName($value) : string
    {
        return match($value) {
            self::REQUESTED => 'Requested',
            self::APPROVED => 'Approved',
            self::CANCELLED => 'Cancelled'
        };
    }
}
