<?php

namespace App\States;

use App\Enum\TravelOrderStatus;
use App\Models\TravelOrder;

interface TravelOrderState {
    public function transitionTo(TravelOrder $travelOrder, TravelOrderStatus $newStatus): void;
}