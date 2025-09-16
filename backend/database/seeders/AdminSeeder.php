<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user admin pertama
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'bio' => 'Administrator utama sistem',
        ]);

        // Buat beberapa user contoh
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'active',
            'bio' => 'User biasa',
        ]);

        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'role' => 'moderator',
            'status' => 'active',
            'bio' => 'Moderator konten',
        ]);

        $this->command->info('Admin dan user contoh berhasil dibuat!');
        $this->command->info('Email: admin@example.com, Password: password');
    }
}
