<?php

namespace Database\Seeders;

use DB;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::updateOrCreate(['name' => 'Admin'], 
                    [
                        'name' => 'Admin',
                        'guard_name' => 'web',
                        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]
                );

        $manager = Role::updateOrCreate(['name' => 'Project Manager'],
                    [
                    'name' => 'Project Manager',
                    'guard_name' => 'web',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                    ]
                );

        $member = Role::updateOrCreate(['name' => 'Team Member'],
                [
                  'name' => 'Team Member',
                  'guard_name' => 'web',
                  'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                  'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]);

        $admin->givePermissionTo(Permission::all());
        $manager->givePermissionTo(Permission::all());
        $member->givePermissionTo(['task_read', 'task_status_update']);
    }
}
