<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    private int $user_id;
    private string $user_email;
    private string $user_username;
    private string $user_name;
    private string $user_avatar;
    private string $user_banner;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user_id = $user->id;
        $this->user_email = $user->email ?? '';
        $this->user_username = $user->username ?? '';
        $this->user_name = $user->name ?? '';
        $this->user_avatar = $user->avatar ?? '';
        $this->user_banner = $user->banner ?? '';
    }

    /**
     * Get the notification's database type.
     *
     * @return string
     */
    public function databaseType(object $notifiable): string
    {
        return 'user-registered';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $via = [];

        if ($notifiable->wantsNotificationsFor(type: 'user_registered', via: 'mail') !== false) $via[] = 'mail';
        if ($notifiable->wantsNotificationsFor(type: 'user_registered', via: 'database') !== false) $via[] = 'database';

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject(__('Ein Nutzer hat sich registriert'))
        ->greeting(__('Hallo :name,', ['name' => $notifiable->name]))
        ->line(__('Der Nutzer **:name** (:email) hat sich soeben registriert. Bislang sind keine Aktionen erforderlich.', ['name' => $this->user_name, 'email' => $this->user_email]))
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
            'id' => $this->user_id,
            'email' => $this->user_email,
            'username' => $this->user_username,
            'name' => $this->user_name,
            'avatar' => $this->user_avatar,
            'banner' => $this->user_banner,
        ];
    }
}
