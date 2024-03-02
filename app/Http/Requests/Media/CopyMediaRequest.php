<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class CopyMediaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'paths' => ['required', 'array'],
            'paths.*' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
        ];
    }
}
