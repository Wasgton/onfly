<?php

namespace Tests\Feature;

use App\Enum\TravelOrderStatus;
use App\Exceptions\FailToCreateException;
use App\Models\TravelOrder;
use App\Service\AuthService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class TravelOrderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @throws FailToCreateException
     */
    public function test_should_create_a_new_travel_order(): void
    {
        $user = $this->createUser();
        $token = app(AuthService::class)->login(['email' => $user['email'], 'password' => $user['password']])['access_token'];
        $travelData = [
            'destination' => $this->faker->city,
            'departure_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'return_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
        ];
        $this->postJson(route('api.v1.travel_orders.store'), $travelData, ['Authorization' => 'Bearer '.$token])
             ->assertStatus(Response::HTTP_CREATED);
    }
    
    /**
     * @throws FailToCreateException
     */
    public function test_should_not_create_a_new_travel_order_with_missing_data(): void
    {
        $user = $this->createUser();
        $token = app(AuthService::class)->login(['email' => $user['email'], 'password' => $user['password']])['access_token'];
        $travelData = [
            'destination' => '',
            'departure_date' => '',
            'return_date' => '',
        ];
        $this->postJson(route('api.v1.travel_orders.store'), $travelData, ['Authorization' => 'Bearer '.$token])
             ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['destination', 'departure_date', 'return_date']);
    }

    
    /**
     * @throws FailToCreateException
     */
    public function test_should_not_create_a_new_travel_order_when_non_authenticated(): void
    {
        $travelData = [
            'destination'    => $this->faker->city,
            'departure_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'return_date'    => Carbon::now()->addDays(10)->format('Y-m-d'),
        ];

        $this->postJson(route('api.v1.travel_orders.store'), $travelData)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
    
    public function test_should_show_travel_order_details(): void
    {
        $user = $this->createUser();
        $this->actingAs($user['user']);
        $token = app(AuthService::class)->login(['email' => $user['email'], 'password' => $user['password']]
        )['access_token'];

        $travelData = [
            'destination'    => $this->faker->city,
            'departure_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'return_date'    => Carbon::now()->addDays(10)->format('Y-m-d'),
        ];
        $travelOrder = TravelOrder::factory()->create($travelData);
        $this->getJson(route('api.v1.travel_orders.show', ['travelOrder' => $travelOrder->id]),
            ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson(['data'=>[
                'id' => $travelOrder->id,
                'applicant' => $travelOrder->applicant_name,
                'destination' => $travelOrder->destination,
                'departure_date' => $travelOrder->departure_date->format('Y-m-d H:i:s'),
                'return_date' => $travelOrder->return_date->format('Y-m-d H:i:s'),
                'status' =>  TravelOrderStatus::toName($travelOrder->status),
            ]]);
    }
    
    public function test_should_not_show_travel_order_details_without_authentication(): void
    {
        $user = $this->createUser();
        $travelOrder = TravelOrder::factory()->create([
            'user_id' => $user['user']->id,
            'applicant_name' => $user['user']->name,
        ]);
        $this->getJson(route('api.v1.travel_orders.show', ['travelOrder' => $travelOrder->id]))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_should_not_show_non_existent_travel_order(): void
    {
        $user = $this->createUser();
        $token = app(AuthService::class)->login(['email' => $user['email'], 'password' => $user['password']]
        )['access_token'];

        $response = $this->getJson(route('api.v1.travel_orders.show', ['travelOrder' => 999999]),
            ['Authorization' => 'Bearer ' . $token])
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    
    
}
