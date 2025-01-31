<?php

namespace App\States;

use App\Enum\TravelOrderStatus;
use App\Models\TravelOrder;
use DomainException;

class RequestedState implements TravelOrderState
{
    public function transitionTo(TravelOrder $travelOrder, TravelOrderStatus $newStatus): void
    {
        if ($travelOrder->id_user === auth()->user()->id){
            //todo finish
            throw new DomainException("You can't approve your own order");
        }
        $travelOrder->status = $newStatus;
    }
}