<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Athlete>
 */
class AthleteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'   => $this->faker->name(),
            'bod'    => $this->faker->date(),
            'gender' => $this->faker->randomElement(['male','female']),
            'club_id'=> \App\Models\Club::inRandomOrder()->value('id'), // pastikan club sudah ada
        ];
    }
}
