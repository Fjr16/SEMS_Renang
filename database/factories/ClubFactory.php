<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Club>
 */
class ClubFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'club_role_category_id' => \App\Models\ClubRoleCategory::inRandomOrder()->value('id'), // pastikan category sudah ada
            'club_code' => strtoupper($this->faker->bothify('CLB###??')),
            'club_name' => $this->faker->company(),
            'club_city' => $this->faker->city(),
            'club_province' => $this->faker->state(),
            'club_lead' => $this->faker->name(),
            'lead_phone' => $this->faker->phoneNumber(),
            'team_type' => $this->faker->randomElement(['school', 'club', 'city', 'province', 'nation']),
            'club_logo' => null,
        ];
    }
}
