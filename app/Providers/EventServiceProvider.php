<?php

namespace App\Providers;

use App\Events\TwoFactorMethodEnabled;
use App\Events\UserBlocked;
use App\Events\UserEnabled;
use App\Events\UserUnblocked;
use App\Listeners\GenerateTwoFactorBackupCodes;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\SendAdministrativeUserRegisteredNotification;
use App\Listeners\SendAdministrativeUserVerifiedTheirEmailNotification;
use App\Listeners\SendBlockedNotification;
use App\Listeners\SendEnabledNotification;
use App\Listeners\SendUnblockedNotification;
use App\Listeners\SetDefaultTwoFactorMethod;
use App\Listeners\VerifyTwoFactorViaRemember;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            SendAdministrativeUserRegisteredNotification::class,
        ],
        Login::class => [
            LogSuccessfulLogin::class,
            VerifyTwoFactorViaRemember::class,
        ],
        Verified::class => [
            SendAdministrativeUserVerifiedTheirEmailNotification::class,
        ],
        UserEnabled::class => [
            SendEnabledNotification::class,
        ],
        UserBlocked::class => [
            SendBlockedNotification::class,
        ],
        UserUnblocked::class => [
            SendUnblockedNotification::class,
        ],
        TwoFactorMethodEnabled::class => [
            GenerateTwoFactorBackupCodes::class,
            SetDefaultTwoFactorMethod::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
