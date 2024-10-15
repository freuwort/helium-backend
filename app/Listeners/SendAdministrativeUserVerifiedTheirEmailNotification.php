<?php

namespace App\Listeners;

use App\Classes\Permissions\Permissions;
use App\Models\User;
use App\Notifications\UserVerifiedTheirEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendAdministrativeUserVerifiedTheirEmailNotification implements ShouldQueue
{
    public function handle(object $event): void
    {
        if (!!$event->user->enabled_at) return;

        $users = User::query()
        ->whereEmailVerified()
        ->whereEnabled()
        ->whereCan(Permissions::SYSTEM_ACCESS_ADMIN_PANEL)
        ->whereCan(Permissions::SYSTEM_VIEW_USERS)
        ->whereCan(Permissions::SYSTEM_ENABLE_USERS)
        ->get();

        Notification::send($users, new UserVerifiedTheirEmail($event->user));
    }
}
