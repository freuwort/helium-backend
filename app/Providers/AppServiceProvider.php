<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enforce https
        if (config('app.force_https')) {
            URL::forceScheme('https');
        }

        // Set verification email response
        VerifyEmail::toMailUsing(function (User $user, string $url) {
            return (new MailMessage)
            ->subject(__('Email Adresse bestätigen'))
            ->greeting(__('Hallo :name,', ['name' => $user->name ?? $user->username ?? __('neuer Benutzer')]))
            ->line(__('Bitte bestätigen Sie Ihre Email Adresse, indem Sie den folgenden Link klicken.'))
            ->action(__('Email Adresse bestätigen'), $url)
            ->line(__('Wenn Sie kein Konto bei :app erstellt haben, können Sie diese Email schlicht ignorieren.', ['app' => config('app.name')]))
            ->salutation(__('– :app Benachrichtigungen', ['app' => config('app.name')]));
        });
    }
}
