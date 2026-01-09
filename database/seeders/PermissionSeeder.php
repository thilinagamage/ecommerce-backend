<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $permissions = [
            'view_dashboard',

            'view_users',
            'create_user',
            'edit_user',
            'delete_user',
            'update_user',
            'assign_roles',
            'reset_passwords',
            'activate_users',
            'deactivate_users',


            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'update_products',

            'view_orders',
            'create_orders',
            'edit_orders',
            'delete_orders',
            'update_orders',

            'view_reports',
            'manage_settings',

        ];

        foreach($permissions as $permission){
            Permission::firstOrCreate(['name' => $permission]);

        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $staff = Role::firstOrCreate(['name' => 'staff']);

        $superAdmin->givePermissionTo(Permission::all());
        $admin->syncPermissions([
            'view_dashboard',

            'view_users',
            'create_user',
            'edit_user',

            'view_products',
            'create_products',
            'edit_products',

            'view_orders',
            'create_orders',
            'edit_orders',
            'delete_orders',
            'update_orders',

            'view_reports',
        ]);

        $staff->syncPermissions([
            'view_dashboard',
            'view_products',
            'view_orders',
            'update_orders',
            'view_reports',
        ]);
    }
}
