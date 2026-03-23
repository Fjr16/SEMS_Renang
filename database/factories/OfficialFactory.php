<?php

namespace Database\Factories;

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
            'license' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{6}'),
            'club_id'=> \App\Models\Club::inRandomOrder()->value('id'), // pastikan club sudah ada
        ];
    }
}
