<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\OrderStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
     protected $model = \App\Models\Order::class;

    public function definition():array
    {
        return [
            'user_id' => \App\Models\User::factory()->state(['role' => 'attendee']),
            'event_id' => \App\Models\Event::factory(),
            'total_amount' => $this->faker->numberBetween(50, 500),
  'status' => OrderStatus::PENDING,
            'payment_intent_id' => $this->faker->uuid(),
        ];
}
}
