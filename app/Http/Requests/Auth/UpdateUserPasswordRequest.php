<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class UpdateUserPasswordRequest extends FormRequest
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
            'password' => 'required',
            'new_password' => 'required|min:8',
        ];
    }

    /**
     * Configure the validator instance.
     * 
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ( !Hash::check($this->password, $this->user()->password) )
            {
                $validator->errors()->add('password', 'The provided password does not match your current password.');
            }
        });
    }
}
