<?php

namespace Database\Seeders;

use App\Models\Athlete;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Official;
use App\Models\Organization;
use App\Models\Pool;
use App\Models\User;
use App\Models\Venue;
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
        $this->call(AgeGroupSeeder::class);
        $this->call(UserRolePermissionSeeder::class);

        DB::table('users')->truncate();
        User::factory(100)->create();

        DB::table('clubs')->truncate();
        Club::factory(50)->create();

        DB::table('athletes')->truncate();
        Athlete::factory(500)->create();

        DB::table('officials')->truncate();
        Official::factory(250)->create();

        DB::table('organizations')->truncate();
        Organization::factory(100)->create();

        // DB::table('venues')->truncate();
        Venue::factory(100)->create();

        DB::table('pools')->truncate();
        Pool::factory(50)->create();

        DB::table('competitions')->truncate();
        Competition::factory(200)->create();

        $this->call([
            UserRolePermissionSeeder::class,
        ]);
        $user = \App\Models\User::orderBy('id')->first();
        if ($user) {
            $user->assignRole('super_admin');
        }

        // create dummy Entries
        $competition_team_id = DB::table('competition_teams')->insertGetId([
            'competition_id' => 105,
            'team_id' => 4,
            'status' => 'pending',
            'total_fee' => 0,
            'payment_status' => 'unpaid'
        ]);
        DB::table('competition_team_officials')->insert([
            'competition_team_id' => $competition_team_id,
            'official_id' => 62,
            'role_override' => 'Manajer Tim',
        ]);
        DB::table('competition_team_officials')->insert([
            'competition_team_id' => $competition_team_id,
            'official_id' => 71,
            'role_override' => 'Pelatih Kepala',
        ]);

        DB::table('competition_entries')->insert([
            'competition_team_id' => $competition_team_id,
            'athlete_id' => 48,
            'competition_event_id' => 1,
            'is_relay' => false,
            'entry_time' => '11.11.11',
            // 'seed_time' => ,
            'status' => 'pending',
        ]);
        DB::table('competition_entries')->insert([
            'competition_team_id' => $competition_team_id,
            'athlete_id' => 232,
            'competition_event_id' => 1,
            'is_relay' => false,
            'entry_time' => '11.11.11',
            // 'seed_time' => ,
            'status' => 'pending',
        ]);

        DB::table('competition_entries')->insert([
            'competition_team_id' => $competition_team_id,
            'athlete_id' => 2,
            'competition_event_id' => 2,
            'is_relay' => false,
            'entry_time' => '11.11.11',
            // 'seed_time' => ,
            'status' => 'pending',
        ]);
        DB::table('competition_entries')->insert([
            'competition_team_id' => $competition_team_id,
            'athlete_id' => 12,
            'competition_event_id' => 2,
            'is_relay' => false,
            'entry_time' => '11.11.11',
            // 'seed_time' => ,
            'status' => 'pending',
        ]);
        $entryIdRelay = DB::table('competition_entries')->insertGetId([
            'competition_team_id' => $competition_team_id,
            'athlete_id' => null,
            'competition_event_id' => 6,
            'is_relay' => true,
            'entry_time' => '12.12.12',
            // 'seed_time' => ,
            'status' => 'pending',
        ]);

        DB::table('competition_entry_relay_members')->insert([
            'competition_entry_id' => $entryIdRelay,
            'athlete_id' => 71,
            'leg_order' => 1,
            'status' => 'active',
        ]);
        DB::table('competition_entry_relay_members')->insert([
            'competition_entry_id' => $entryIdRelay,
            'athlete_id' => 89,
            'leg_order' => 2,
            'status' => 'active',
        ]);
        DB::table('competition_entry_relay_members')->insert([
            'competition_entry_id' => $entryIdRelay,
            'athlete_id' => 319,
            'leg_order' => 3,
            'status' => 'active',
        ]);
    }
}
