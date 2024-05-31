<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class DestroyManyEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:events,id'],
        ];
    }
}
