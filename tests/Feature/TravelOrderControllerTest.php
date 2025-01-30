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
        $token = $this->getToken();

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

    public function test_should_filter_travel_orders_by_destination(): void
    {
        $token = $this->getToken();        
        $destination = 'New York';
        TravelOrder::factory()->count(3)->create(['destination' => $destination]);
        TravelOrder::factory()->count(2)->create(['destination' => 'Los Angeles']);
        
        $response = $this->getJson(route('api.v1.travel_orders.get-all', ['destination' => $destination]),
            ['Authorization' => 'Bearer ' . $token]);
        
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(3, 'data')
            ->assertJsonFragment(['destination' => $destination]);
    }
    
    public function test_should_filter_travel_orders_by_departure_dates(): void
    {
        $token = $this->getToken();
        $startDate = Carbon::now()->addDays(5)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(10)->format('Y-m-d');
        TravelOrder::factory()->create(['departure_date' => Carbon::now()->addDays(6)->format('Y-m-d')]);
        TravelOrder::factory()->create(['departure_date' => Carbon::now()->addDays(8)->format('Y-m-d')]);
        TravelOrder::factory()->create(['departure_date' => Carbon::now()->addDays(12)->format('Y-m-d')]);

        $response = $this->getJson(route('api.v1.travel_orders.get-all', [
            'departure_period_start' => $startDate,
            'departure_period_end'   => $endDate,
        ]), ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data');
    }

    /**
     * @throws FailToCreateException
     */
    public function test_should_filter_travel_orders_by_status(): void
    {
        $token = $this->getToken();
        $travelOrders = TravelOrder::factory()->count(4)->create();
        $travelOrders[0]->setState(TravelOrderStatus::APPROVED);
        $travelOrders[1]->setState(TravelOrderStatus::APPROVED);
        $travelOrders[2]->setState(TravelOrderStatus::CANCELLED);
        $travelOrders[3]->setState(TravelOrderStatus::CANCELLED);
        $travelOrders->each(function ($travelOrder) {
            $travelOrder->save();
        });
        
        $response = $this->getJson(route('api.v1.travel_orders.get-all', ['status' => TravelOrderStatus::APPROVED]),['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['status' => TravelOrderStatus::toName(TravelOrderStatus::APPROVED)]);
        
        $response = $this->getJson(route('api.v1.travel_orders.get-all', ['status' => TravelOrderStatus::CANCELLED]),['Authorization' => 'Bearer ' . $token]);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['status' => TravelOrderStatus::toName(TravelOrderStatus::CANCELLED)]);
    }

    /**
     * @throws FailToCreateException
     */
    public function test_should_filter_travel_orders_with_multiple_filters(): void
    {
        $token = $this->getToken();

        $destination = 'Miami';
        $status = TravelOrderStatus::APPROVED;
        $startDate = Carbon::now()->addDays(5)->format('Y-m-d');
        $endDate = Carbon::now()->addDays(10)->format('Y-m-d');

        $travelOrder = TravelOrder::factory()->create([
            'destination'    => $destination,
            'departure_date' => Carbon::now()->addDays(6)->format('Y-m-d'),
        ]);
        $travelOrder->setState($status);
        $travelOrder->save();
        
        $travelOrder = TravelOrder::factory()->create([
            'destination'    => $destination,
            'departure_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
        ]);
        $travelOrder->setState($status);
        $travelOrder->save();

        $travelOrder = TravelOrder::factory()->create([
            'destination'    => 'Los Angeles',
            'departure_date' => Carbon::now()->addDays(6)->format('Y-m-d'),
        ]);
        $travelOrder->setState($status);
        $travelOrder->save();

        $response = $this->getJson(route('api.v1.travel_orders.get-all', [
            'destination'          => $destination,
            'status'               => $status,
            'departure_date_start' => $startDate,
            'departure_date_end'   => $endDate,
        ]), ['Authorization' => 'Bearer ' . $token]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['destination' => $destination])
            ->assertJsonFragment(['status' => TravelOrderStatus::toName($status)]);
    }

    /**
     * @return mixed
     * @throws FailToCreateException
     */
    public function getToken(): mixed
    {
        $user = $this->createUser();
        $this->actingAs($user['user']);
        return app(AuthService::class)->login(['email' => $user['email'], 'password' => $user['password']]
        )['access_token'];
    }


}
