<?php

namespace App\Http\Requests\Media;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class ShareMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'path' => ['required', 'exists:media,src_path'],
            'inherit_access' => ['required', 'boolean'],
            'public_access' => ['nullable', 'string', 'in:read,write,admin'],
            'access' => ['nullable', 'array'],
            'access.*.permissible_id' => ['required', 'integer'],
            'access.*.permissible_type' => ['required', 'string', 'in:user,role'],
            'access.*.permission' => ['required', 'string', 'in:read,write,admin'],
        ];
    }

    // transform permissible_type from 'user' to User::class (and vice versa for role)
    public function passedValidation(): void
    {
        $this->merge([
            'access' => collect($this->access)->map(fn ($access) => [
                'permissible_id' => $access['permissible_id'],
                'permissible_type' => self::typeDict($access['permissible_type']),
                'permission' => $access['permission'],
            ]),
        ]);
    }

    private function typeDict(string $type): string
    {
        return match ($type) {
            'user' => User::class,
            'role' => Role::class,
        };
    }
}
