<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('age_groups')->insert([
            [
                'label' => 'KU I',
                'min_age' => '16',
                'max_age' => '18',
            ],
            [
                'label' => 'KU II',
                'min_age' => '14',
                'max_age' => '15',
            ],
            [
                'label' => 'KU III',
                'min_age' => '12',
                'max_age' => '13',
            ],
            [
                'label' => 'KU IV',
                'min_age' => '10',
                'max_age' => '11',
            ],
            [
                'label' => 'KU IV',
                'min_age' => null,
                'max_age' => '9',
            ],
            [
                'label' => 'UMUM',
                'min_age' => '0',
                'max_age' => null,
            ],
        ]);
    }
}
