<?php

namespace App\Http\Requests\User;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUsernameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Setting::getSetting('policy_allow_username_change', false);
    }

    
    
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
        ];
    }
}
