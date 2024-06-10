<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            // Model
            'model.event_id' => ['required', 'exists:events,id'],
            'model.user_id' => ['nullable', 'exists:users,id'],
            'model.form_id' => ['nullable', 'exists:forms,id'],
            'model.type' => ['nullable', 'string', 'max:255'],
            'model.name' => ['nullable', 'string', 'max:255'],
            'model.email' => ['nullable', 'email'],
            'model.phone' => ['nullable', 'string', 'max:255'],
            'model.code' => ['nullable', 'string', 'max:127', 'unique:event_invites,code,'.$this->id],
            'model.status' => ['nullable', 'string', 'in:maybe,accepted,rejected'],
        ];
    }
}
