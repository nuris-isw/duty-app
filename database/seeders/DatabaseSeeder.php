<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'ess@admin.com'], // Cegah duplikasi kalau seed dijalankan berulang
            [
                'name' => 'Administrator',
                'password' => Hash::make('170845'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
