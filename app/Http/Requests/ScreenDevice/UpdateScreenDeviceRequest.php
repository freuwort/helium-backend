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
            'playlists.*.id' => ['exists:screen_playlists,id'],
            'playlists.*.from_date' => ['nullable', 'date_format:Y-m-d'],
            'playlists.*.from_time' => ['nullable', 'date_format:H:i:s'],
            'playlists.*.to_date' => ['nullable', 'date_format:Y-m-d'],
            'playlists.*.to_time' => ['nullable', 'date_format:H:i:s'],
            'playlists.*.on_days' => ['nullable', 'array'],
            'playlists.*.on_days.*' => ['in:0,1,2,3,4,5,6'],
            'playlists.*.on_screen' => ['nullable', 'string', 'max:255'],
        ];
    }



    public function passedValidation()
    {
        $this->merge(['playlists' => collect($this->input('playlists'))->mapWithKeys(function ($item) {
            return [$item['id'] => [
                'from_date' => $item['from_date'] ?? null,
                'from_time' => $item['from_time'] ?? null,
                'to_date' => $item['to_date'] ?? null,
                'to_time' => $item['to_time'] ?? null,
                'on_days' => $item['on_days'] ?? [],
                'on_screen' => $item['on_screen'] ?? null,
            ]];
        })->toArray()]);
    }
}
