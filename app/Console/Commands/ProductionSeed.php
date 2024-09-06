<?php

namespace App\Console\Commands;

use App\Classes\Data\Countries;
use App\Classes\Data\Currencies;
use App\Classes\Permissions\Permissions;
use App\Models\Country;
use App\Models\Currency;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class ProductionSeed extends Command
{
    protected $signature = 'seed:production';
    protected $description = 'Seeds the database with production data.';

    public function handle()
    {
        $this->line('');
        $this->line('  <fg=white;bg=blue> INFO </> Seeding for production.');
        $this->line('');

        $timestamp = now();
        $this->line('  <fg=yellow;bg=default>RUNNING</> Seeding Countries.');
        $this->seedCountries();
        $this->line('  <fg=green;bg=default>DONE</>    <fg=white;bg=default>Seeding Countries.</> ('.now()->diffInMilliseconds($timestamp).' ms)');
        $this->line('');

        $timestamp = now();
        $this->line('  <fg=yellow;bg=default>RUNNING</> Seeding Currencies.');
        $this->seedCurrencies();
        $this->line('  <fg=green;bg=default>DONE</>    <fg=default;bg=default>Seeding Currencies.</> ('.now()->diffInMilliseconds($timestamp).' ms)');
        $this->line('');

        $timestamp = now();
        $this->line('  <fg=yellow;bg=default>RUNNING</> Seeding Permissions.');
        $this->seedPermissions();
        $this->line('  <fg=green;bg=default>DONE</>    <fg=white;bg=default>Seeding Permissions.</> ('.now()->diffInMilliseconds($timestamp).' ms)');
        $this->line('');
    }

    private function seedCountries()
    {
        foreach (Countries::get() as $country) {
            Country::updateOrCreate([ 'code' => $country['code'] ], $country);
        }
    }

    private function seedCurrencies()
    {
        foreach (Currencies::get() as $currency) {
            Currency::updateOrCreate([ 'code' => $currency['code'] ], $currency);
        }
    }

    private function seedPermissions()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = Permissions::all(includeSuperAdmin: true);

        foreach ($permissions as $permission)
        {
            Permission::updateOrCreate([ 'name' => $permission ]);
        }

        Permission::whereNotIn('name', $permissions)->delete();
    }
}
