<?php

namespace Tests\Feature;

use App\Enum\TravelOrderStatus;
use App\Events\StatusChangeEvent;
use App\Exceptions\FailToCreateException;
use App\Models\TravelOrder;
use App\Service\TravelOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class TravelOrderServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @throws FailToCreateException
     */
    public function test_event_dispatched_when_status_changes()
    {
        Event::fake();
        $user = $this->createUser();
        $this->actingAs($user['user']);
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user['user']->id,
            'applicant_name' => $user['user']->name,
        ])->fresh();

        $service = app(TravelOrderService::class);
        $service->approve($travelOrder);

        Event::assertDispatched(StatusChangeEvent::class, function ($event) use ($travelOrder) {
            return $event->travelOrder->id === $travelOrder->id && $event->travelOrder->status === TravelOrderStatus::APPROVED;
        });
    }
    
}
