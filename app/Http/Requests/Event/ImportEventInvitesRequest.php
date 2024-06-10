<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class ImportEventInvitesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            'items.*.user_id' => ['nullable', 'exists:users,id'],
            'items.*.form_id' => ['nullable', 'exists:forms,id'],
            'items.*.type' => ['nullable', 'string', 'max:255'],
            'items.*.name' => ['nullable', 'string', 'max:255'],
            'items.*.email' => ['nullable', 'email'],
            'items.*.phone' => ['nullable', 'string', 'max:255'],
            'items.*.code' => ['nullable', 'string', 'max:127', 'unique:event_invites,code'],
            'items.*.status' => ['nullable', 'string', 'in:maybe,accepted,rejected'],
        ];
    }
}
