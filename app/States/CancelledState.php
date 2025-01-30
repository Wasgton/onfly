<?php

namespace App\States;

use App\Enum\TravelOrderStatus;
use App\Models\TravelOrder;

class CancelledState implements TravelOrderState
{
    public function transitionTo(TravelOrder $travelOrder, TravelOrderStatus $newStatus): void
    {
        throw new \DomainException("It's not possible to change the status of a cancelled order");
    }
}