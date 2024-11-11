<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    private string $url;
    private string $user_email;
    private string $user_name;
    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, string $url)
    {
        $this->url = $url;
        $this->user_email = $user->email ?? '';
        $this->user_name = $user->user_info->fullname ?? $user->name ?? '';
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
        ->subject(__('Ihr Passwort wiederherstellen'))
        ->greeting(__('Hallo :name,', ['name' => $this->user_name]))
        ->line(__('Wir haben eine Anfrage zum Zurücksetzen Ihres Passworts erhalten. Klicken Sie auf den folgenden Link um Ihr Passwort zu zurücksetzen. **Dieser Link ist nur :minutes Minuten gültig.**', ['minutes' => config('auth.passwords.users.expire')]))
        ->action(__('Passwort zurücksetzen'), $this->url)
        ->line(__('Wenn Sie diesen Link nicht angefordert haben, können Sie diese Email schlicht ignorieren.'))
        ->salutation(__('– :organization Benachrichtigungen', ['organization' => config('app.name')]));
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
