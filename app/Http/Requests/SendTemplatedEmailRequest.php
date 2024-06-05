<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendTemplatedEmailRequest extends FormRequest
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
            'ids' => ['required', 'array'],
            'cc' => ['nullable', 'string'],
            'bcc' => ['nullable', 'string'],
            'subject' => ['nullable', 'string'],
            'message' => ['nullable', 'string'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['required', 'string', 'exists:media,src_path'],
        ];
    }
}
