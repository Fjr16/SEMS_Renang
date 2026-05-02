<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pool>
 */
class PoolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'venue_id' => \App\Models\Venue::inRandomOrder()->value('id'),
            'code' => $this->faker->unique->bothify('##??'),
            'name' => $this->faker->word(),
            'pool_role' => 'competition',
            'course_type' => $this->faker->randomElement(['SCM','LCM','SCY']),
            'length_meter' => $this->faker->randomElement([25,50]),
            'total_lanes' => 8,
            'depth' => $this->faker->randomFloat(2,0,20),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
