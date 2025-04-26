<?php

namespace App\Listeners;

use App\Notifications\UserEnabled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendEnabledNotification implements ShouldQueue
{
    public function handle(object $event): void
    {
        $event->user->notify(new UserEnabled($event->user));
    }
}
