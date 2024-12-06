<?php

namespace App\Http\Requests\Auth;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Setting::getSetting('policy_allow_name_change', false);
    }

    
    
    public function rules(): array
    {
        return [
            'salutation' => ['nullable', 'string', 'max:255'],
            'prefix' => ['nullable', 'string', 'max:255'],
            'firstname' => ['nullable', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'lastname' => ['nullable', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:255'],
        ];
    }
}
