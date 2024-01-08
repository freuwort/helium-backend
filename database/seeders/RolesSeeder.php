<?php

namespace Database\Seeders;

use App\Classes\Permissions\Permissions;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::updateOrCreate(['name' => 'Admin']);
        $admin->syncPermissions([Permissions::SYSTEM_ADMIN, Permissions::SYSTEM_ACCESS_ADMIN_PANEL]);

        $personal = Role::updateOrCreate(['name' => 'Personal']);
        $personal->syncPermissions([Permissions::SYSTEM_ACCESS_ADMIN_PANEL]);
    }
}
