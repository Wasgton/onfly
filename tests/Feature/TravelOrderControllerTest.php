<?php

namespace Tests\Feature;

use App\Exceptions\FailToCreateException;
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


}
