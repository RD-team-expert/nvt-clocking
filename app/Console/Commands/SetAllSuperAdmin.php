<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
class SetAllSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-all-super-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
          // Fetch all permission names
        // 1) Collect ALL existing permissions
        $allPermissions = Permission::pluck('name')->toArray();

        // 2) Ensure Super Admin role exists
        $superAdmin = Role::firstOrCreate(['name' => 'super admin']);

        // 3) Give it every permission
        $superAdmin->syncPermissions($allPermissions);

        // 4) Assign Super Admin role to every user
        $users = User::all();
        foreach ($users as $user) {
            $user->assignRole($superAdmin);
        }

        $this->info('🎉 All users have been granted the Super Admin role with full permissions.');

    }
}
