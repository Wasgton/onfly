<?php

namespace Tests\Feature;

use App\Enum\TravelOrderStatus;
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
    public function createUser(): array
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];
        app(AuthService::class)->createUser($userData);

        return $userData;
    }

    /**
     * @throws FailToCreateException
     */
    public function test_should_create_a_new_travel_order(): void
    {
        $user = $this->createUser();
        $token = app(AuthService::class)->login(['email' => $user['email'], 'password' => $user['password']])['access_token'];
        $travelData = [
            'destination' => $this->faker->city,
            'departure_date' => Carbon::parse(Carbon::now()->format('Y-m-d'))->addDays(5)->format('Y-m-d'),
            'return_date' => Carbon::parse(Carbon::now()->format('Y-m-d'))->addDays(10)->format('Y-m-d'),
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
