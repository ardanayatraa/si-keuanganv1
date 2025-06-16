<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Cek apakah sudah ada admin dengan username ini
        if (!Admin::where('username', 'admin')->exists()) {
            Admin::create([
                'username' => 'admin',
                'email'    => 'admin@example.com',
                'password' => Hash::make('password'),
            ]);

            $this->command->info('Admin default berhasil dibuat: username `admin`, email `admin@example.com`, password `password`');
        } else {
            $this->command->warn('Admin dengan username `admin` sudah ada. Seeder dilewati.');
        }
    }
}
