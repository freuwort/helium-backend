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
        $this->call(DomainSettingSeeder::class);
        $this->call(CountriesSeeder::class);
        $this->call(CurrenciesSeeder::class);



        // Test Users
        \App\Models\User::factory(60)->create()->each(function ($user) {
            $user->assignRole('Personal');
        });

        // Test Companies
        \App\Models\Company::factory(20)->create();
    }
}
