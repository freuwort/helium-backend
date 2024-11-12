<?php

namespace App\Listeners;

use App\Notifications\UserUnblocked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendUnblockedNotification
{
    public function handle(object $event): void
    {
        $event->user->notify(new UserUnblocked($event->user));
    }
}
