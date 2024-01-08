<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionsSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(SuperAdminSeeder::class);

        // Test Users
        \App\Models\User::factory(10)->create()->each(function ($user) {
            $user->assignRole('Personal');
        });
    }
}
