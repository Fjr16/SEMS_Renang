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
            'gender' => $this->faker->randomElement(['M','F']),
            'school_name' => $this->faker->company(),
            'club_id'=> \App\Models\Club::inRandomOrder()->value('id'), // pastikan club sudah ada
            'city_name' => $this->faker->city(),
            'province_name' => $this->faker->state(),
        ];
    }
}
