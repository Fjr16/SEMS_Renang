<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Str;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Wajib: reset cache spatie
        // DB::table('role_has_permissions')->delete();
        // DB::table('model_has_roles')->delete();
        // DB::table('model_has_permissions')->delete();
        // DB::table('roles')->delete();
        // DB::table('permissions')->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'web';

        /**
         * Daftar permission by module.
         * Kamu bisa tambah sesuai kebutuhan.
         */
        $permissionsByModule = [
            'my_teams' => [
                'Tim Saya.Pendaftaran Kompetisi',
                'Tim Saya.Dashboard',

                'Tim Saya.Kelola Atlet-List',
                'Tim Saya.Kelola Atlet-Tambah',
                'Tim Saya.Kelola Atlet-Ubah',
                'Tim Saya.Kelola Atlet-Hapus',

                'Tim Saya.Kelola Official-List',
                'Tim Saya.Kelola Official-Tambah',
                'Tim Saya.Kelola Official-Ubah',
                'Tim Saya.Kelola Official-Hapus',
            ],
            'master_settings' => [
                'Master Setting.Klub-List',
                'Master Setting.Klub-Tambah',
                'Master Setting.Klub-Ubah',
                'Master Setting.Klub-Hapus',

                'Master Setting.Atlet-List',
                'Master Setting.Atlet-Tambah',
                'Master Setting.Atlet-Ubah',
                'Master Setting.Atlet-Hapus',

                'Master Setting.Official-List',
                'Master Setting.Official-Tambah',
                'Master Setting.Official-Ubah',
                'Master Setting.Official-Hapus',

                'Master Setting.Kompetisi-List',
                'Master Setting.Kompetisi-Tambah',
                'Master Setting.Kompetisi-Ubah',
                'Master Setting.Kompetisi-Hapus',
                'Master Setting.Kompetisi-Kelola',

                'Master Setting.Lokasi & Kolam-List',
                'Master Setting.Lokasi & Kolam-Tambah',
                'Master Setting.Lokasi & Kolam-Ubah',
                'Master Setting.Lokasi & Kolam-Hapus',

                'Master Setting.Kelompok Umur-List',
                'Master Setting.Kelompok Umur-Tambah',
                'Master Setting.Kelompok Umur-Ubah',
                'Master Setting.Kelompok Umur-Hapus',

                'Master Setting.Organisasi-List',
                'Master Setting.Organisasi-Tambah',
                'Master Setting.Organisasi-Ubah',
                'Master Setting.Organisasi-Hapus',

                'Master Setting.User Hak Akses-List',
                'Master Setting.User Hak Akses-Tambah User',
                'Master Setting.User Hak Akses-Ubah User',
                'Master Setting.User Hak Akses-Hapus User',
                'Master Setting.User Hak Akses-Tambah Role',
                'Master Setting.User Hak Akses-Tambah Role User',
                'Master Setting.User Hak Akses-Tambah Hak Akses Role',
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

        // $roles = [
        //     'admin',
        //     'penyelenggara',
        //     'panitia',
        //     'manajer_tim',
        // ];

        // foreach ($roles as $roleName) {
        //     Role::firstOrCreate([
        //         'name' => $roleName,
        //         'guard_name' => $guard,
        //     ]);
        // }

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        // $admin = Role::where('name', 'admin')->first();
        $admin->givePermissionTo($allPermissions);
        $user = User::firstOrCreate([
                'name' => 'admin',
                'email' => 'admin@gmail.com',
            ],[
            'organization_id' => null,
            'club_id' => null,
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
        ]);
        $user->assignRole('admin');

        // reset cache lagi setelah sync
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
