<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = \App\Models\OrderItem::class;

    public function definition():array
    {
        return [
            'order_id' => \App\Models\Order::factory(),
            'ticket_type_id' => \App\Models\TicketType::factory(),
            'quantity' => $this->faker->numberBetween(1, 5),
            'price' => $this->faker->numberBetween(10, 200),
        ];
}
}
