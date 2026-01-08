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
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'web';

        /**
         * Daftar permission by module.
         * Kamu bisa tambah sesuai kebutuhan.
         */
        $permissionsByModule = [
            'users' => [
                'users.view',
                'users.create',
                'users.update',
                'users.delete',
                'users.roles.assign',
                'users.permissions.assign',
            ],
            'roles' => [
                'roles.view',
                'roles.create',
                'roles.update',
                'roles.delete',
                'roles.permissions.assign',
            ],
            'permissions' => [
                'permissions.view',
                'permissions.create',
                'permissions.update',
                'permissions.delete',
            ],
            'athletes' => [
                'athletes.view',
                'athletes.create',
                'athletes.update',
                'athletes.delete',
                'athletes.import',
                'athletes.export',
            ],
            'clubs' => [
                'clubs.view',
                'clubs.create',
                'clubs.update',
                'clubs.delete',
            ],
            'competitions' => [
                'competitions.view',
                'competitions.create',
                'competitions.update',
                'competitions.delete',
                'competitions.publish',
                'competitions.schedule.manage',
            ],
            'events' => [
                'events.view',
                'events.create',
                'events.update',
                'events.delete',
                'events.order.manage',
            ],
            'entries' => [
                'entries.view',
                'entries.create',
                'entries.update',
                'entries.delete',
                'entries.import',
                'entries.approve',
            ],
            'heats' => [
                'heats.generate',
                'heats.view',
                'heats.update',
                'heats.lock',
            ],
            'results' => [
                'results.view',
                'results.input',
                'results.publish',
                'results.export.pdf',
                'results.export.excel',
                'results.splits.view',
            ],
            'reports' => [
                'reports.medal_tally.view',
                'reports.best_swimmer.view',
                'reports.new_records.view',
                'reports.full_pdf.view',
            ],
        ];

        // Flatten semua permission
        $allPermissions = collect($permissionsByModule)->flatten()->unique()->values();

        // Buat permission (idempotent: aman kalau di-run berulang)
        foreach ($allPermissions as $permName) {
            Permission::firstOrCreate([
                'name' => $permName,
                'guard_name' => $guard,
            ]);
        }

        /**
         * Buat roles
         */
        $roles = [
            'super_admin',
            'admin',
            'official',
            'club_manager',
            'guest', // optional kalau kamu ingin role untuk user publik yang login
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
        $roleSuperAdmin = Role::where('name', 'super_admin')->where('guard_name', $guard)->first();
        $roleAdmin      = Role::where('name', 'admin')->where('guard_name', $guard)->first();
        $roleOfficial   = Role::where('name', 'official')->where('guard_name', $guard)->first();
        $roleClubMgr    = Role::where('name', 'club_manager')->where('guard_name', $guard)->first();
        $roleGuest      = Role::where('name', 'guest')->where('guard_name', $guard)->first();

        // super_admin: semua permission
        $roleSuperAdmin?->syncPermissions(Permission::where('guard_name', $guard)->get());

        // admin: hampir semua kecuali yang super sensitive (kalau ada)
        $adminPerms = $allPermissions->toArray();
        $roleAdmin?->syncPermissions(
            Permission::where('guard_name', $guard)->whereIn('name', $adminPerms)->get()
        );

        // official: event, heats, results, reports (tanpa manage user/role/permission)
        $officialPerms = [
            // competitions/events/entries/heats/results/reports (view + manage sesuai peran official)
            'competitions.view',
            'competitions.schedule.manage',

            'events.view',
            'events.create',
            'events.update',
            'events.order.manage',

            'entries.view',
            'entries.create',
            'entries.update',
            'entries.approve',

            'heats.generate',
            'heats.view',
            'heats.update',
            'heats.lock',

            'results.view',
            'results.input',
            'results.publish',
            'results.export.pdf',
            'results.export.excel',
            'results.splits.view',

            'reports.medal_tally.view',
            'reports.best_swimmer.view',
            'reports.new_records.view',
            'reports.full_pdf.view',
        ];
        $roleOfficial?->syncPermissions(
            Permission::where('guard_name', $guard)->whereIn('name', $officialPerms)->get()
        );

        // club_manager: kelola atlet/entries milik klub + lihat kompetisi & hasil
        $clubPerms = [
            'athletes.view',
            'athletes.create',
            'athletes.update',
            'athletes.delete',

            'entries.view',
            'entries.create',
            'entries.update',

            'clubs.view',

            'competitions.view',
            'events.view',

            'results.view',
            'results.splits.view',

            'reports.medal_tally.view',
            'reports.best_swimmer.view',
            'reports.new_records.view',
            'reports.full_pdf.view',
        ];
        $roleClubMgr?->syncPermissions(
            Permission::where('guard_name', $guard)->whereIn('name', $clubPerms)->get()
        );

        // guest: read-only publik (opsional)
        $guestPerms = [
            'athletes.view',
            'clubs.view',
            'competitions.view',
            'events.view',
            'results.view',
            'reports.medal_tally.view',
            'reports.best_swimmer.view',
            'reports.new_records.view',
            'reports.full_pdf.view',
        ];
        $roleGuest?->syncPermissions(
            Permission::where('guard_name', $guard)->whereIn('name', $guestPerms)->get()
        );

        // reset cache lagi setelah sync
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
