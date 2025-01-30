<?php

namespace Tests\Unit;

use App\Enum\TravelOrderStatus;
use App\Exceptions\FailToCreateException;
use App\Models\TravelOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use ValueError;

class TravelOrderTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @throws FailToCreateException
     */
    public function test_initial_status_is_requested(): void
    {
        $user = $this->createUser()['user'];
        $this->actingAs($user);
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => auth()->id(),
            'applicant_name' => auth()->user()->name
        ]);
        $this->assertEquals(TravelOrderStatus::REQUESTED, $travelOrder->status);
    }

    public function test_should_trigger_exception_when_manually_change_status(): void
    {
        $user = $this->createUser()['user'];
        $this->actingAs($user);
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => auth()->id(),
            'applicant_name' => auth()->user()->name
        ]);
        $this->expectException(ValueError::class);
        $travelOrder->status = 5;
    }

    /**
     * @throws FailToCreateException
     */
    public function test_status_requested_can_transition_to_any_status(): void
    {
        $user = $this->createUser()['user'];
        $this->actingAs($user);
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => auth()->id(),
            'applicant_name' => auth()->user()->name
        ]);

        $possibleStatuses = [
            TravelOrderStatus::APPROVED,
            TravelOrderStatus::CANCELLED,
        ];

        foreach ($possibleStatuses as $status) {
            $travelOrder->setState($status);
            $this->assertEquals($status, $travelOrder->status);
        }
    }

    public function test_status_cancelled_cannot_transition_to_other_status(): void
    {
        $user = $this->createUser()['user'];
        $this->actingAs($user);
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => auth()->id(),
            'applicant_name' => auth()->user()->name,
        ]);
        $travelOrder->setState(TravelOrderStatus::CANCELLED);
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("It's not possible to change the status of a cancelled order");
        $travelOrder->setState(TravelOrderStatus::APPROVED);
    }

    public function test_status_approved_can_transition_to_cancelled_with_valid_departure_date(): void
    {
        $user = $this->createUser()['user'];
        $this->actingAs($user);
        $travelOrder = TravelOrder::factory()->create([
            'departure_date' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'user_id' => auth()->id(),
            'applicant_name' => auth()->user()->name,
        ]);
        $travelOrder->setState(TravelOrderStatus::APPROVED);
        $travelOrder->setState(TravelOrderStatus::CANCELLED);
        $this->assertEquals(TravelOrderStatus::CANCELLED, $travelOrder->status);
    }
    
    public function test_status_approved_cannot_transition_to_cancelled_with_invalid_departure_date(): void
    {
        $user = $this->createUser()['user'];
        $this->actingAs($user);
        $travelOrder = TravelOrder::factory()->create([
            'departure_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'user_id' => auth()->id(),
            'applicant_name' => auth()->user()->name,
        ]);
        $travelOrder->setState(TravelOrderStatus::APPROVED);
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('An approved order can only be cancelled 2 days before departure date');
        $travelOrder->setState(TravelOrderStatus::CANCELLED);
    }
    
}
