<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            // Model
            'model.name' => ['nullable', 'string', 'max:255'],
            'model.slug' => ['nullable', 'string', 'max:255', 'unique:events,slug,'.$this->event->id],
            'model.description' => ['nullable', 'string'],
            'model.start_at' => ['nullable', 'date'],
            'model.end_at' => ['nullable', 'date'],
        ];
    }
}
