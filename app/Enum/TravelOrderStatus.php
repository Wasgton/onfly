<?php

namespace App\Enum;

enum TravelOrderStatus : int
{
    case REQUESTED = 1;
    case APPROVED = 2;
    case CANCELLED = 3;
}
