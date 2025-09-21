<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\TicketStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
      protected $model = \App\Models\Ticket::class;

    public function definition():array
    {
        return [
            'ticket_type_id' => \App\Models\TicketType::factory(),
            'attendee_id' => \App\Models\User::factory()->state(['role' => 'attendee']),
            'qr_code' => $this->faker->uuid(),
              'status' => TicketStatus::VALID,
        ];
    }
}
