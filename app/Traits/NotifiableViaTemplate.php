<?php

namespace App\Traits;

use App\Notifications\BlankMessage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait NotifiableViaTemplate
{
    private function getTemplateResource()
    {
        return $this->templateResourceClass::make($this);
    }


    private function template(string|null $template, array $resource): string
    {
        if (!$template) return '';

        return Str::of($template)->replaceMatches('/{{(.*?)}}/', function ($match) use ($resource) {
            return $resource[$match[1]] ?? '';
        });
    }


    public function sendTemplatedEmail(array $data)
    {
        $resource = (array) $this->getTemplateResource()->resolve();

        $cc = $this->template($data['cc'], $resource);
        $bcc = $this->template($data['bcc'], $resource);
        $subject = $this->template($data['subject'], $resource);
        $message = $this->template($data['message'], $resource);
        $attachments = $data['attachments'] ?? [];

        $this->notify(new BlankMessage($subject, $message, $attachments, $cc, $bcc));
    }
}