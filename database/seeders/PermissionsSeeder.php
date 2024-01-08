<?php

namespace Database\Seeders;

use App\Classes\Permissions\Permissions;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = Permissions::all();

        foreach ($permissions as $permission)
        {
            Permission::updateOrCreate([ 'name' => $permission ]);
        }
    }
}
