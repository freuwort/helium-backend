<?php

namespace App\Listeners;

use App\Events\TwoFactorMethodEnabled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SetDefaultTwoFactorMethod
{
    public function handle(TwoFactorMethodEnabled $event): void
    {
        // If no two factor method is enabled, exit early
        if (!$event->model->has_tfa_enabled) return;

        // If no default method is set, use the first enabled method
        if (!$event->model->default_tfa_method)
        {
            $method = $event->model->twoFactorMethods->firstWhere('enabled', true);
            $event->model->setDefaultTfaMethod($method->type);
        }
    }
}
