<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserEnabled extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject(__('Ihr Konto bei :app wurde freigegeben!', ['app' => config('app.name')]))
        ->greeting(__('Hallo :name,', ['name' => $notifiable->name]))
        ->line(__('Ihr Konto wurde soeben von einem Administrator freigegeben und Sie können sich jetzt einloggen.'))
        ->action(__('Jetzt einloggen'), config('app.frontend_url'))
        ->salutation(__('– :app Benachrichtigungen', ['app' => config('app.name')]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
