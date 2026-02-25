<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\Club;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->truncate();
        User::factory(100)->create();

        DB::table('clubs')->truncate();
        Club::factory(50)->create();

        DB::table('athletes')->truncate();
        Athlete::factory(500)->create();

        $this->call([
            UserRolePermissionSeeder::class,
        ]);
        $user = \App\Models\User::orderBy('id')->first();
        if ($user) {
            $user->assignRole('super_admin');
        }
    }
}
