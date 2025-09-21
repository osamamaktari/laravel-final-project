<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\EventStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     *
     */
        protected $model = \App\Models\Event::class;
    public function definition(): array
    {
            $start = $this->faker->dateTimeBetween('+1 days', '+30 days');
        $end = $this->faker->dateTimeBetween($start, '+35 days');

        return [
            'organizer_id' => \App\Models\User::factory()->state(['role' => 'organizer']),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'venue' => $this->faker->address(),
            'start_date' => $start,
            'end_date' => $end,
            'banner_url' => 'https://via.placeholder.com/600x200',
  'status' => EventStatus::PENDING,
        ];
    }
}
