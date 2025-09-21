<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketType>
 */
class TicketTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
       protected $model = \App\Models\TicketType::class;

    public function definition():array
    {
        return [
            'event_id' => \App\Models\Event::factory(),
            'name' => $this->faker->word() . ' Ticket',
            'price' => $this->faker->numberBetween(10, 200),
            'quantity' => $this->faker->numberBetween(50, 500),
            'sold' => 0,
        ];
    }
    }

