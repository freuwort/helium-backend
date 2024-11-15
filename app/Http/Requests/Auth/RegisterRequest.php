<?php

namespace App\Http\Requests\Auth;

use App\Classes\Auth\RegistrationConfig;
use App\Models\Role;
use App\Models\Setting;
use App\Rules\Address;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
{
    private Object $config;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Setting::getSetting('policy_allow_registration', false);
    }

    /**
     * Prepare the data for validation.
     */
    public function prepareForValidation()
    {
        request()->validate([
            'profiles' => ['present', 'array'],
            'profiles.*' => ['required', 'string'],
        ]);

        $profiles = Setting::getSetting('registration_profiles', []);
        $profileIds = request()->input('profiles');

        $this->config = (new RegistrationConfig())
            ->load($profiles)
            ->select($profileIds)
            ->getOrElse(fn ($status) => abort($status->code, $status->message));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return collect([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', Rules\Password::defaults()],
            'gdpr' => ['required', 'accepted'],
            'terms' => ['required', 'accepted'],

            'salutation' => ['required', 'string', 'max:255'],
            'prefix' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'suffix' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', 'max:255'],
            'legalname' => ['required', 'string', 'max:255'],

            'organisation' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'job_title' => ['required', 'string', 'max:255'],

            'customer_id' => ['required', 'string', 'max:255'],
            'employee_id' => ['required', 'string', 'max:255'],
            'member_id' => ['required', 'string', 'max:255'],

            'main_address' => ['required', new Address],
            'billing_address' => ['required', new Address],
            'shipping_address' => ['required', new Address],
        ])
        ->only($this->config->fields)
        ->toArray();
    }

    /**
     * Prepare the data for further processing.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        if ($this->config->autoEnable) {
            $validated['auto_enable'] = true;
        }

        if (count($this->config->roles)) {
            $validated['roles'] = Role::whereIn('name', $this->config->roles)->pluck('id')->toArray();
        }

        return $validated;
    }
}