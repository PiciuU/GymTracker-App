<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'user_role_id' => 2,
            'name' => 'Administrator',
            'login' => 'admin',
            'password' => Hash::make('gymtracker2023'),
            'email' => 'gymtracker@dev.dream-speak.pl',
        ]);

        User::factory()->count(5)->create();
    }
}
