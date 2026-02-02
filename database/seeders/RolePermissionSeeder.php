<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ===== PERMISSIONS =====
        $permissions = [
            'taxcut.view',
            'taxcut.create',
            'taxcut.edit',
            'taxcut.delete',

            'user.view',
            'user.create',
            'user.edit',
            'user.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ===== ROLES =====
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $accounting = Role::firstOrCreate(['name' => 'accounting']);
        $viewer = Role::firstOrCreate(['name' => 'viewer']);

        // ===== ASSIGN =====
        $admin->givePermissionTo(Permission::all());

        $accounting->givePermissionTo([
            'taxcut.view',
            'taxcut.create',
            'taxcut.edit',
        ]);

        $viewer->givePermissionTo([
            'taxcut.view',
        ]);
    }
}
