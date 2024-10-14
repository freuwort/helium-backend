<?php

namespace App\Listeners;

use App\Classes\Permissions\Permissions;
use App\Models\User;
use App\Notifications\UserVerifiedTheirEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendAdministrativeUserVerifiedTheirEmailNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $users = User::query()
        ->whereEmailVerified()
        ->whereEnabled()
        ->whereCan([Permissions::SYSTEM_ENABLE_USERS])
        ->get();

        Notification::send($users, new UserVerifiedTheirEmail($event->user));
    }
}
