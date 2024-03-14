<?php

namespace App\Listeners;

use App\Events\TwoFactorMethodEnabled;

class GenerateTwoFactorBackupCodes
{
    public function handle(TwoFactorMethodEnabled $event): void
    {
        if (!$event->model->has_tfa_backup_codes) $event->model->generateTfaBackupCodes();
    }
}
