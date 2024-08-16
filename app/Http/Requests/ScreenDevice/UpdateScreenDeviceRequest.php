<?php

namespace App\Http\Requests\ScreenDevice;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScreenDeviceRequest extends FormRequest
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
            'model.group' => ['nullable', 'string', 'max:255'],

            // Address
            'address' => ['nullable', 'array'],
            'address.address_line_1' => ['nullable', 'string', 'max:255'],
            'address.address_line_2' => ['nullable', 'string', 'max:255'],
            'address.city' => ['nullable', 'string', 'max:255'],
            'address.state' => ['nullable', 'string', 'max:255'],
            'address.postal_code' => ['nullable', 'string', 'max:255'],
            'address.country_code' => ['nullable', 'exists:countries,code'],
            'address.latitude' => ['nullable', 'numeric'],
            'address.longitude' => ['nullable', 'numeric'],
            'address.notes' => ['nullable', 'string', 'max:255'],

            // Playlists
            'playlists' => ['nullable', 'array'],
            'playlists.*.id' => ['exists:playlists,id'],
            'playlists.*.from_date' => ['nullable', 'date'],
            'playlists.*.from_time' => ['nullable', 'time'],
            'playlists.*.to_date' => ['nullable', 'date'],
            'playlists.*.to_time' => ['nullable', 'time'],
            'playlists.*.on_days' => ['nullable', 'array'],
            'playlists.*.on_days.*' => ['in:0,1,2,3,4,5,6'],
            'playlists.*.on_screen' => ['nullable', 'string', 'max:255'],
        ];
    }



    // after validation passed: transform data
    // TODO
}
