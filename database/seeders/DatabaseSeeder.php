<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Company;
use App\Models\Event;
use App\Models\User;
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
        User::factory(60)->create()->each(function ($user) {
            $user->assignRole('Personal');
        });

        // Test Companies
        Company::factory(20)->create();

        // Test Events
        Event::factory(20)->create();
    }
}
