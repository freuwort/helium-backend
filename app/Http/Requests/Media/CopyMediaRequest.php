<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class CopyMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paths' => ['required', 'array'],
            'paths.*' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
        ];
    }
}
