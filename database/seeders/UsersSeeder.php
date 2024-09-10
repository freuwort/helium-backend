<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(60)->create()->each(function ($user) {
            $user->assignRole('Personal');
        });
    }
}
