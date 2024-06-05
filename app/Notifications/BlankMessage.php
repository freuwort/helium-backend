<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class BlankMessage extends Notification
{
    use Queueable;

    private $cc;
    private $bcc;
    private $subject;
    private $message;
    private $attachments;

    public function __construct($subject, $message, $attachments = null, $cc = null, $bcc = null)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->attachments = $attachments;
        $this->cc = $cc;
        $this->bcc = $bcc;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }



    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->subject)
            ->greeting(new HtmlString($this->subject))
            ->line(new HtmlString($this->message))
            ->salutation(new HtmlString('<small>Versendet durch '.config('app.name').'</small>'));

        if ($this->cc) $mail->cc($this->cc);
        if ($this->bcc) $mail->bcc($this->bcc);

        if ($this->attachments)
        {
            foreach ($this->attachments as $attachment)
            {
                $mail->attach($attachment);
            }
        }

        return $mail;
    }



    public function toArray(object $notifiable): array
    {
        return [];
    }
}
