<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            // Model
            'model.parent_id' => ['nullable', 'exists:events,id'],
            'model.name' => ['nullable', 'string', 'max:255'],
            'model.slug' => ['nullable', 'string', 'max:255', 'unique:events,slug'],
            'model.description' => ['nullable', 'string'],
            'model.start_at' => ['nullable', 'date'],
            'model.end_at' => ['nullable', 'date'],
        ];
    }
}
