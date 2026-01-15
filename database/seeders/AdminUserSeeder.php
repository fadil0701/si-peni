<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah user admin sudah ada
        $adminExists = User::where('email', 'pusdatinppkp@gmail.com')->exists();

        if (!$adminExists) {
            User::create([
                'name' => 'Administrator',
                'email' => 'pusdatinppkp@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('Admin@123'), // Default password: password
                'remember_token' => Str::random(10),
            ]);

            $this->command->info('✅ User admin berhasil dibuat!');
            $this->command->info('📧 Email: pusdatinppkp@gmail.com');
            $this->command->info('🔑 Password: Admin@123');
            $this->command->warn('⚠️  Jangan lupa ubah password setelah login pertama kali!');
        } else {
            $this->command->warn('⚠️  User admin sudah ada, melewati pembuatan user admin.');
        }
    }
}
