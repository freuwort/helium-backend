<?php

namespace App\Http\Requests\ScreenDevice;

use Illuminate\Foundation\Http\FormRequest;

class DestroyManyScreenDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    
    
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:screen_devices,id'],
        ];
    }
}
