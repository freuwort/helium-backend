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
        $roles = [
            ['Admin',       'shield',           'rgb(244 63 94)',   [Permissions::SYSTEM_ADMIN, Permissions::SYSTEM_ACCESS_ADMIN_PANEL]],
            ['Personal',    'person',           'rgb(99 102 241)',  [Permissions::SYSTEM_ACCESS_ADMIN_PANEL]],
            ['Kunde',       'shopping_cart',    'rgb(251 146 60)',  []],
            ['Logistik',    'local_shipping',   'rgb(168 85 247)',  []]
        ];

        foreach ($roles as $role)
        {
            $r = Role::updateOrCreate(['name' => $role[0], 'icon' => $role[1], 'color' => $role[2]]);
            $r->syncPermissions($role[3]);
        }
    }
}
