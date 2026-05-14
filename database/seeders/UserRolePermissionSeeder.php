<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Wajib: reset cache spatie
        DB::table('role_has_permissions')->delete();
        DB::table('model_has_roles')->delete();
        DB::table('model_has_permissions')->delete();
        DB::table('roles')->delete();
        DB::table('permissions')->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'web';

        /**
         * Daftar permission by module.
         * Kamu bisa tambah sesuai kebutuhan.
         */
        $permissionsByModule = [
            'my_teams' => [
                'team-saya.profil team',
                'team-saya.daftar kompetisi',
                'team-saya.kelola atlet',
                'team-saya.kelola official',
            ],
            'master_settings' => [
                'master-settings.klub',
                'master-settings.atlet',
                'master-settings.official',
                'master-settings.kompetisi',
                'master-settings.lokasi & kolam',
                'master-settings.master kelompok umur',
                'master-settings.master organisasi',
                'master-settings.manajemen user & hak akses',
            ],
        ];

        // Flatten semua permission
        $allPermissions = collect($permissionsByModule)->flatten()->unique()->values();

        foreach ($allPermissions as $permName) {
            Permission::firstOrCreate([
                'name' => $permName,
                'guard_name' => $guard,
            ]);
        }

        $roles = [
            'admin',
            'penyelenggara',
            'panitia',
            'manajer_tim',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => $guard,
            ]);
        }

        /**
         * Assign permissions ke roles (mapping)
         */
        // $roleSuperAdmin = Role::where('name', 'super_admin')->where('guard_name', $guard)->first();
        // $roleAdmin      = Role::where('name', 'admin')->where('guard_name', $guard)->first();
        // $roleOfficial   = Role::where('name', 'official')->where('guard_name', $guard)->first();
        // $roleClubMgr    = Role::where('name', 'club_manager')->where('guard_name', $guard)->first();
        // $roleGuest      = Role::where('name', 'guest')->where('guard_name', $guard)->first();

        // $roleSuperAdmin?->syncPermissions(Permission::where('guard_name', $guard)->get());

        // $adminPerms = $allPermissions->toArray();
        // $roleAdmin?->syncPermissions(
        //     Permission::where('guard_name', $guard)->whereIn('name', $adminPerms)->get()
        // );

        // $officialPerms = [
        //     'competitions.view',
        //     'competitions.schedule.manage',

        //     'events.view',
        //     'events.create',
        //     'events.update',
        //     'events.order.manage',

        //     'entries.view',
        //     'entries.create',
        //     'entries.update',
        //     'entries.approve',

        //     'heats.generate',
        //     'heats.view',
        //     'heats.update',
        //     'heats.lock',

        //     'results.view',
        //     'results.input',
        //     'results.publish',
        //     'results.export.pdf',
        //     'results.export.excel',
        //     'results.splits.view',

        //     'reports.medal_tally.view',
        //     'reports.best_swimmer.view',
        //     'reports.new_records.view',
        //     'reports.full_pdf.view',
        // ];
        // $roleOfficial?->syncPermissions(
        //     Permission::where('guard_name', $guard)->whereIn('name', $officialPerms)->get()
        // );

        // // club_manager: kelola atlet/entries milik klub + lihat kompetisi & hasil
        // $clubPerms = [
        //     'athletes.view',
        //     'athletes.create',
        //     'athletes.update',
        //     'athletes.delete',

        //     'entries.view',
        //     'entries.create',
        //     'entries.update',

        //     'clubs.view',

        //     'competitions.view',
        //     'events.view',

        //     'results.view',
        //     'results.splits.view',

        //     'reports.medal_tally.view',
        //     'reports.best_swimmer.view',
        //     'reports.new_records.view',
        //     'reports.full_pdf.view',
        // ];
        // $roleClubMgr?->syncPermissions(
        //     Permission::where('guard_name', $guard)->whereIn('name', $clubPerms)->get()
        // );

        // // guest: read-only publik (opsional)
        // $guestPerms = [
        //     'athletes.view',
        //     'clubs.view',
        //     'competitions.view',
        //     'events.view',
        //     'results.view',
        //     'reports.medal_tally.view',
        //     'reports.best_swimmer.view',
        //     'reports.new_records.view',
        //     'reports.full_pdf.view',
        // ];
        // $roleGuest?->syncPermissions(
        //     Permission::where('guard_name', $guard)->whereIn('name', $guestPerms)->get()
        // );

        // reset cache lagi setelah sync
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
