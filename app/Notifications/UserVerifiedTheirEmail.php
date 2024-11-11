<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserVerifiedTheirEmail extends Notification implements ShouldQueue
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
        $this->user_name = $user->user_info->fullname ?? $user->name ?? '';
        $this->user_avatar = $user->getProfileMedia('avatar') ?? '';
        $this->user_banner = $user->getProfileMedia('banner') ?? '';
    }

    /**
     * Get the notification's database type.
     *
     * @return string
     */
    public function databaseType(object $notifiable): string
    {
        return 'user-verified-their-email';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $via = [];

        if ($notifiable->wantsNotificationsFor(type: 'user_verified', via: 'mail') !== false) $via[] = 'mail';
        if ($notifiable->wantsNotificationsFor(type: 'user_verified', via: 'database') !== false) $via[] = 'database';

        return $via;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject(__('Ein Nutzer wartet auf Ihre Freigabe'))
        ->greeting(__('Hallo :name,', ['name' => $notifiable->user_info->fullname ?? $notifiable->user_info->name]))
        ->line(__('Der Nutzer **:name** (:email) hat sich registriert und deren Email bestätigt. Klicken Sie auf den folgenden Link um den Nutzer freizugeben.', ['name' => $this->user_name, 'email' => $this->user_email]))
        ->action(__('Jetzt freigeben'), config('app.frontend_url').'/users/editor/'.$this->user_id.'?action=enable')
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
            'id' => $this->user_id,
            'email' => $this->user_email,
            'username' => $this->user_username,
            'name' => $this->user_name,
            'avatar' => $this->user_avatar,
            'banner' => $this->user_banner,
        ];
    }
}
