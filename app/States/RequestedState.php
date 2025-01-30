<?php

namespace App\States;

use App\Enum\TravelOrderStatus;
use App\Models\TravelOrder;

class RequestedState implements TravelOrderState
{
    public function transitionTo(TravelOrder $travelOrder, TravelOrderStatus $newStatus): void
    {
        $travelOrder->status = $newStatus;
    }
}