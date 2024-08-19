<?php

namespace App\Http\Requests\ScreenPlaylist;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScreenPlaylistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            // Model
            'model.type' => ['nullable', 'string', 'in:playlist'],
            'model.name' => ['nullable', 'string', 'max:255'],
            'model.screen_order' => ['nullable', 'array'],
            'model.screen_order.*' => ['nullable', 'integer', 'exists:screens,id'],

            // Screens
            'screens' => ['nullable', 'array'],
            'screens.*.id' => ['exists:screens,id'],
            'screens.*.from_date' => ['nullable', 'date_format:Y-m-d'],
            'screens.*.from_time' => ['nullable', 'date_format:H:i:s'],
            'screens.*.to_date' => ['nullable', 'date_format:Y-m-d'],
            'screens.*.to_time' => ['nullable', 'date_format:H:i:s'],
            'screens.*.on_days' => ['nullable', 'array'],
            'screens.*.on_days.*' => ['in:0,1,2,3,4,5,6'],
        ];
    }



    public function passedValidation()
    {
        $this->merge(['screens' => collect($this->input('screens'))->mapWithKeys(function ($item) {
            return [$item['id'] => [
                'from_date' => $item['from_date'] ?? null,
                'from_time' => $item['from_time'] ?? null,
                'to_date' => $item['to_date'] ?? null,
                'to_time' => $item['to_time'] ?? null,
                'on_days' => $item['on_days'] ?? [],
            ]];
        })->toArray()]);
    }
}
