<?php

namespace App\Console\Commands;

use App\Classes\Permissions\Permissions;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class InitiateSuperUser extends Command
{
    protected $signature = 'superuser:init';
    protected $description = 'Create the super user using environment variables.';

    public function handle()
    {
        if ($this->hasSuperUser())
        {
            $this->line('');
            $this->line('  <fg=white;bg=yellow> INFO </> Super user already exists. No super user created.');
            $this->line('');
            return;
        }

        if (!$this->hasEnv())
        {
            $this->line('');
            $this->line('  <fg=white;bg=yellow> WARN </> Environment variables not set. No super user created.');
            $this->line('');
            return;
        }

        $this->createSuperUser();

        $this->line('');
        $this->line('  <fg=white;bg=blue> INFO </> Created the super user.');
        $this->line('');
    }

    private function hasSuperUser()
    {
        return User::permission(Permissions::SYSTEM_SUPER_ADMIN)->exists();
    }

    private function hasEnv()
    {
        return !!env('SUPER_USER_EMAIL') && !!env('SUPER_USER_PASSWORD');
    }

    private function createSuperUser()
    {
        $superuser = User::create([
            'username' => 'superuser',
            'email' => env('SUPER_USER_EMAIL'),
            'password' => Hash::make(env('SUPER_USER_PASSWORD')),
            'email_verified_at' => now(),
            'enabled_at' => now(),
            'firstname' => 'Super',
            'lastname' => 'User',
        ]);

        $superuser->givePermissionTo(Permissions::SYSTEM_SUPER_ADMIN);
    }
}
