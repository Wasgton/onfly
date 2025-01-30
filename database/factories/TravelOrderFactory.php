<?php

namespace Database\Factories;

use App\Models\TravelOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TravelOrderFactory extends Factory
{
    protected $model = TravelOrder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null, 
            'applicant_name' => $this->faker->name,
            'destination' => $this->faker->city,
            'departure_date' => now()->addDays(5),
            'return_date' => now()->addDays(10)
        ];
    }
}
