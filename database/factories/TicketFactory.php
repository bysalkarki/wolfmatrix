<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
    public function definition(): array
    {
        return [
            'event_name' => $this->faker->sentence(3),
            'phone' => $this->faker->phoneNumber,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'ticket_code' => $this->faker->unique()->uuid,
            'user_id' => null,
            'created_at' => now(),
        ];
    }
}
