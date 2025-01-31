<?php

namespace App\Listeners;

use App\Events\StatusChangeEvent;
use App\Notifications\OrderStatusChangeNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendApprovedNotificationListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(){}

    /**
     * Handle the event.
     */
    public function handle(StatusChangeEvent $event): void
    {
        $travelOrder = $event->travelOrder;
        dd($travelOrder);
        $travelOrder->user->notify(new OrderStatusChangeNotification($travelOrder));
    }
}
