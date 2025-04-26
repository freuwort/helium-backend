<?php

namespace App\Listeners;

use App\Notifications\UserUnblocked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendUnblockedNotification implements ShouldQueue
{
    public function handle(object $event): void
    {
        $event->user->notify(new UserUnblocked($event->user));
    }
}
