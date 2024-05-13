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
            'access' => ['required', 'array'],
            'access.*.model_id' => ['required', 'integer'],
            'access.*.model_type' => ['required', 'string', 'in:user,role'],
            'access.*.permission' => ['required', 'string', 'in:read,write,admin'],
        ];
    }

    // transform model_type from 'user' to User::class (and vice versa for role)
    public function passedValidation(): void
    {
        $this->merge([
            'access' => collect($this->access)->map(fn ($access) => [
                'model_id' => $access['model_id'],
                'model_type' => self::typeDict($access['model_type']),
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
