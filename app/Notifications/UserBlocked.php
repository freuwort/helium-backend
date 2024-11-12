<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserBlocked extends Notification
{
    use Queueable;

    private User $user;
    private string|null $reason;
    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, ?string $reason)
    {
        $this->user = $user;
        $this->reason = $reason ?? null;
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
        ->subject(__('Ihr Konto bei :app wurde gesperrt!', ['app' => config('app.name')]))
        ->greeting(__('Hallo :name,', ['name' => $notifiable->name]))
        ->line(__('Ihr Konto wurde soeben von einem Administrator gesperrt.'))
        ->lineIf(!!$this->reason, __('Grund: **:reason**', ['reason' => $this->reason]))
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
