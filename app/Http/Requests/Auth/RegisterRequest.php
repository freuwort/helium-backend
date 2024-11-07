<?php

namespace App\Http\Requests\Auth;

use App\Classes\Auth\RegistrationValidator;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
{
    private RegistrationValidator $registrationValidator;
    private Object $registrationValidation;

    /**
     * Get the available validation rules.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    private function ruleset(): Array
    {
        return [
            'email' => ['email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email']],
            'phone' => ['phone' => ['required', 'string', 'max:255']],
            'username' => ['username' => ['required', 'string', 'max:255', 'unique:users,username']],
            'password' => ['password' => ['required', Rules\Password::defaults()]],
            'gdpr' => ['gdpr' => ['required', 'accepted']],

            'salutation' => ['salutation' => ['required', 'string', 'max:255']],
            'prefix' => ['prefix' => ['required', 'string', 'max:255']],
            'firstname' => ['firstname' => ['required', 'string', 'max:255']],
            'middlename' => ['middlename' => ['required', 'string', 'max:255']],
            'lastname' => ['lastname' => ['required', 'string', 'max:255']],
            'suffix' => ['suffix' => ['required', 'string', 'max:255']],
            'nickname' => ['nickname' => ['required', 'string', 'max:255']],
            'legalname' => ['legalname' => ['required', 'string', 'max:255']],

            'organisation' => ['organisation' => ['required', 'string', 'max:255']],
            'department' => ['department' => ['required', 'string', 'max:255']],
            'job_title' => ['job_title' => ['required', 'string', 'max:255']],

            'customer_id' => ['customer_id' => ['required', 'string', 'max:255']],
            'employee_id' => ['employee_id' => ['required', 'string', 'max:255']],
            'member_id' => ['member_id' => ['required', 'string', 'max:255']],

            'main_address' => [
                'main_address.address_line_1' => ['required', 'string', 'max:255'],
                'main_address.address_line_2' => ['nullable', 'string', 'max:255'],
                'main_address.city' => ['nullable', 'string', 'max:255'],
                'main_address.state' => ['nullable', 'string', 'max:255'],
                'main_address.postal_code' => ['nullable', 'string', 'max:255'],
                'main_address.country_code' => ['nullable', 'exists:countries,code'],
            ],

            'billing_address' => [
                'billing_address.address_line_1' => ['required', 'string', 'max:255'],
                'billing_address.address_line_2' => ['nullable', 'string', 'max:255'],
                'billing_address.city' => ['nullable', 'string', 'max:255'],
                'billing_address.state' => ['nullable', 'string', 'max:255'],
                'billing_address.postal_code' => ['nullable', 'string', 'max:255'],
                'billing_address.country_code' => ['nullable', 'exists:countries,code'],
            ],

            'shipping_address' => [
                'shipping_address.address_line_1' => ['required', 'string', 'max:255'],
                'shipping_address.address_line_2' => ['nullable', 'string', 'max:255'],
                'shipping_address.city' => ['nullable', 'string', 'max:255'],
                'shipping_address.state' => ['nullable', 'string', 'max:255'],
                'shipping_address.postal_code' => ['nullable', 'string', 'max:255'],
                'shipping_address.country_code' => ['nullable', 'exists:countries,code'],
            ],
        ];
    }

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

        $selection = request()->input('profiles');

        $profiles = Setting::getSetting('registration_profiles', false);

        if (!$profiles) {
            abort(500, 'The registration is not fully configured.');
        }

        $this->registrationValidator = new RegistrationValidator($profiles);
        $this->registrationValidator->loadProfiles($profiles);
        $this->registrationValidator->selectProfiles($selection);

        $this->registrationValidation = $this->registrationValidator->validate();

        if (!$this->registrationValidation->isValid) {
            abort(422, 'The selected profiles are not valid.');
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return collect($this->ruleset())
            ->filter(fn ($rule, $key) => in_array($key, $this->registrationValidation->fields))
            ->reduce(fn ($carry, $rule) => array_merge($carry, $rule), []);
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

        $user = [];
        $toUser = [
            'email',
            'phone',
            'username',
            'password',
        ];

        $userInfo = [];
        $toUserInfo = [
            'salutation',
            'prefix',
            'firstname',
            'middlename',
            'lastname',
            'suffix',
            'nickname',
            'legalname',
            'organisation',
            'department',
            'job_title',
            'customer_id',
            'employee_id',
            'member_id',
        ];

        $mainAddress = null;
        $billingAddress = null;
        $shippingAddress = null;

        // Categorize the validated data for easy processing
        foreach ($validated as $key => $value) {
            if (in_array($key, $toUser)) {
                $user[$key] = $value;
                continue;
            }

            if (in_array($key, $toUserInfo)) {
                $userInfo[$key] = $value;
                continue;
            }

            if ($key === 'main_address') {
                $mainAddress = $value;
                continue;
            }

            if ($key === 'billing_address') {
                $billingAddress = $value;
                continue;
            }

            if ($key === 'shipping_address') {
                $shippingAddress = $value;
                continue;
            }
        }

        return [
            'auto_enable' => $this->registrationValidation->autoEnable,
            'roles' => Role::whereIn('name', $this->registrationValidation->roles)->pluck('id')->toArray(),
            
            'user' => $user,
            'user_info' => $userInfo,
            
            'main_address' => $mainAddress,
            'billing_address' => $billingAddress,
            'shipping_address' => $shippingAddress,
        ];
    }
}