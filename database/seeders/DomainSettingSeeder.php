<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DomainSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'key' => 'company_name',
            'value' => 'Unternehmen',
        ]);

        Setting::create([
            'key' => 'company_legalname',
            'value' => 'Unternehmen GmbH',
        ]);

        Setting::create([
            'key' => 'company_slogan',
            'value' => 'Unternehmen Slogan',
        ]);

        Setting::create([
            'key' => 'company_logo',
            'value' => 'https://img.logoipsum.com/225.svg',
        ]);

        Setting::create([
            'key' => 'default_currency',
            'value' => 'EUR',
        ]);

        Setting::create([
            'key' => 'default_unit_length',
            'value' => 'm',
        ]);

        Setting::create([
            'key' => 'default_unit_weight',
            'value' => 'kg',
        ]);

        Setting::create([
            'key' => 'default_unit_volume',
            'value' => 'l',
        ]);

        Setting::create([
            'key' => 'default_unit_temperature',
            'value' => 'c',
        ]);

        Setting::create([
            'key' => 'default_unit_speed',
            'value' => 'kmh',
        ]);
    }
}
