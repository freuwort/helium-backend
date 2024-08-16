<?php

namespace App\Http\Requests\ScreenPlaylist;

use Illuminate\Foundation\Http\FormRequest;

class DestroyManyScreenPlaylistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:screen_playlists,id'],
        ];
    }
}
