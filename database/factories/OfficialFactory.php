<?php

namespace Database\Factories;

use App\Enums\License;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Official>
 */
class OfficialFactory extends Factory
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
            'role'    => $this->faker->randomElement(['Pelatih Kepala', 'Asisten Pelatih', 'Manajer Tim', 'Dokter Tim', 'Fisioterapis']),
            'gender' => $this->faker->randomElement(['male','female']),
            'license' => $this->faker->randomElement(License::cases()),
            'club_id'=> \App\Models\Club::inRandomOrder()->value('id'), // pastikan club sudah ada
        ];
    }
}
