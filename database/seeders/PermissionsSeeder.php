<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run(): void
    {
        $permissions = [

            // Forms
            'forms.index',
            'forms.create',
            'forms.edit',
            'forms.destroy',

            // Roles
            'roles.index',
            'roles.create',
            'roles.edit',
            'roles.destroy',

            // Clcoking records
            'clcokings.index',
            'clcokings.create',
            'clcokings.edit',
            'clcokings.destroy',

            // Users
            'users.index',
            'users.create',
            'users.edit',
            'users.destroy',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        //super admin role
        $superAdmin = Role::firstOrCreate(['name' => 'super admin']);
        $superAdmin->syncPermissions($permissions);

    }
}
