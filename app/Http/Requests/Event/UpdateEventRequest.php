<?php

namespace App\Http\Requests\Event;

use App\Models\Media;
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

            // Addresses
            'addresses' => ['nullable', 'array'],
            'addresses.*.type' => ['required', 'string', 'max:255'],
            'addresses.*.address_line_1' => ['nullable', 'string', 'max:255'],
            'addresses.*.address_line_2' => ['nullable', 'string', 'max:255'],
            'addresses.*.city' => ['nullable', 'string', 'max:255'],
            'addresses.*.state' => ['nullable', 'string', 'max:255'],
            'addresses.*.postal_code' => ['nullable', 'string', 'max:255'],
            'addresses.*.country_code' => ['nullable', 'exists:countries,code'],
            'addresses.*.latitude' => ['nullable', 'numeric'],
            'addresses.*.longitude' => ['nullable', 'numeric'],
            'addresses.*.notes' => ['nullable', 'string', 'max:255'],

            // Media
            'media' => ['nullable', 'array'],
            'media.*.media_id' => ['required', 'exists:media,id'],
            'media.*.type' => ['required', 'string', 'max:255'],
        ];
    }
}
