<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('club_role_categories')->insert([
            [
                'code' => '001',
                'name' => 'school',
            ],
            [
                'code' => '002',
                'name' => 'club',
            ],
            [
                'code' => '003',
                'name' => 'city',
            ],
            [
                'code' => '004',
                'name' => 'province',
            ],
            [
                'code' => '005',
                'name' => 'nation',
            ],
        ]);
    }

}
