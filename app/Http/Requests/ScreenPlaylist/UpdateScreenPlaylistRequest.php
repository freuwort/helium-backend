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
        ];
    }
}
