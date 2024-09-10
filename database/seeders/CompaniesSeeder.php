<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompaniesSeeder extends Seeder
{
    public function run(): void
    {
        Company::factory(20)->create();
    }
}
