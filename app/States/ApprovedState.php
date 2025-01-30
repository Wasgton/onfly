<?php

namespace App\States;

use App\Enum\TravelOrderStatus;
use App\Models\TravelOrder;
use DomainException;

class ApprovedState implements TravelOrderState
{

    /**
     * @throws DomainException
     */
    public function transitionTo(TravelOrder $travelOrder, TravelOrderStatus $newStatus): void
    {
        if ($newStatus !== TravelOrderStatus::CANCELLED) {
            throw new DomainException("Approved order can only be change to cancelled");
        }
        $departureDate = $travelOrder->departure_date;
        if (!$departureDate || $departureDate->diffInDays(now(), false) > -2) {
            throw new DomainException("An approved order can only be cancelled 2 days before departure date");
        }
        $travelOrder->status = $newStatus; 
    }
}