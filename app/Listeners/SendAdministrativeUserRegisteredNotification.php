<?php

namespace App\Listeners;

use App\Classes\Permissions\Permissions;
use App\Models\User;
use App\Notifications\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendAdministrativeUserRegisteredNotification implements ShouldQueue
{
    public function handle(object $event): void
    {
        $users = User::query()
        ->whereEmailVerified()
        ->whereEnabled()
        ->whereCan(Permissions::SYSTEM_ACCESS_ADMIN_PANEL)
        ->whereCan(Permissions::SYSTEM_VIEW_USERS)
        ->get();

        Notification::send($users, new UserRegistered($event->user));
    }
}
