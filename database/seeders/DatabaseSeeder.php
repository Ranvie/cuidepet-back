<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // UserDTO::factory(10)->create();

        User::factory()->create([
            'name' => 'Test UserDTO',
            'email' => 'test@example.com',
        ]);
    }
}
