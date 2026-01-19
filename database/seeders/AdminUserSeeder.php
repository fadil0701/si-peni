<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin role
        $adminRole = Role::where('name', 'admin')->first();
        
        if (!$adminRole) {
            $this->command->error('Admin role not found! Please run RoleSeeder first.');
            return;
        }

        // Create or update admin user
        $admin = User::firstOrCreate(
            ['email' => 'pusdatinppkp@gmail.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('Admin@123'),
            ]
        );

        // Assign admin role if not already assigned
        if (!$admin->roles->contains($adminRole->id)) {
            $admin->roles()->attach($adminRole->id);
            $this->command->info('Admin role assigned to user: ' . $admin->email);
        } else {
            $this->command->info('Admin user already has admin role.');
        }

        $this->command->info('Admin user created/updated successfully!');
        $this->command->info('Email: pusdatinppkp@gmail.com');
        $this->command->info('Password: Admin@123');
    }
}
