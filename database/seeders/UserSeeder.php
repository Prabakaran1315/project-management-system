<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $managerRole = Role::firstOrCreate(['name' => 'Project Manager']);
        $memberRole = Role::firstOrCreate(['name' => 'Team Member']);

        // Create Admin User
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $adminUser->assignRole($adminRole);

        // Create Project Manager User
        $managerUser = User::create([
            'name' => 'Project Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
        ]);
        $managerUser->assignRole($managerRole);

        // Create Team Member User
        $memberUser = User::create([
            'name' => 'Team Member User',
            'email' => 'member@example.com',
            'password' => Hash::make('password'),
        ]);
        $memberUser->assignRole($memberRole);
    }
}
