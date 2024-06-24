<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        $super_admin = \App\Models\User::create([
            'name' => 'Super Admin',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'remember_token' => null,
            'email_verified_at' => now(),
            'enabled_at' => now(),
        ]);

        $super_admin->user_company()->create([
            'company' => null,
            'department' => null,
            'title' => null,
        ]);

        $super_admin->user_name()->create([
            'salutation' => null,
            'prefix' => null,
            'firstname' => 'Super Admin',
            'middlename' => null,
            'lastname' => null,
            'suffix' => null,
            'nickname' => null,
            'legalname' => null,
        ]);

        // Assign permissions (we expect these to be created in the PermissionsSeeder)
        $super_admin->givePermissionTo(\App\Classes\Permissions\Permissions::SYSTEM_SUPER_ADMIN);

        $super_admin->assignRole(['Admin', 'Personal']);
    }
}
