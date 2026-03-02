<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Competition>
 */
class CompetitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'organization_id' => \App\Models\Organization::inRandomOrder()->value('id'),
            'venue_id' => \App\Models\Venue::inRandomOrder()->value('id'),
            'name' => $this->faker->name,
            'code' => $this->faker->unique()->bothify('##??'),
            'description' => null,
            'start_date' => $this->faker->date('Y-m-d', Carbon::now()->addDay(7)),
            'end_date' => $this->faker->date('Y-m-d', Carbon::now()->addDay(7)),
            'registration_start' => $this->faker->date('Y-m-d', Carbon::now()->addDay(2)),
            'registration_end' => $this->faker->date('Y-m-d', Carbon::now()->addDay(6)),
            'sanction_number' => $this->faker->unique()->bothify('##??'),
            'status' => $this->faker->randomElement(['REGISTRATION', 'RUNNING', 'CLOSED'])
        ];
    }
}
