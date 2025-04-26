<?php

namespace App\Listeners;

use App\Notifications\UserBlocked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBlockedNotification implements ShouldQueue
{
    public function handle(object $event): void
    {
        $event->user->notify(new UserBlocked($event->user, $event->reason));
    }
}
