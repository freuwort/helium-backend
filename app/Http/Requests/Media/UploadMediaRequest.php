<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $allowedMimetypes = [
            'font/*',
            'image/*',
            'text/*',
            'audio/*',
            'video/*',
            'application/*',
        ];
        
        return [
            'files' => ['array', 'max:1000'],
            'files.*' => ['file', 'mimetypes:'.implode(',', $allowedMimetypes)],
            'path' => ['required', 'string', 'max:255'],
        ];
    }
}
