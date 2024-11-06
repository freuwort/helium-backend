<?php

namespace App\Http\Requests\Auth;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
{
    private RegistrationProfile $profile;
    private Collection $profiles;
    private Array $selection;

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
        $this->selection = $this->getChosenProfiles();
        $this->profiles = $this->getProfileSetting();
        $this->profile = new RegistrationProfile($this->profiles);
        $this->profile->select($this->selection);
        $this->profile->getValidation();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return $this->profile->getRules($this->ruleset());
    }



    private function getProfileSetting(): Collection
    {
        $profiles = Setting::getSetting('registration_profiles', false);
        if (!$profiles) throw new \Exception('Registration profiles invalid.');
        
        return collect($profiles);
    }

    private function getChosenProfiles(): Array
    {
        $profiles = $this->input('profiles');
        if (!is_array($profiles)) throw new \Exception('Profile selection invalid.');

        return $profiles;
    }

    

    private function ruleset(): array
    {
        return [
            'email' => ['email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email']],
            'phone' => ['phone' => ['required', 'string', 'max:255']],
            'username' => ['username' => ['required', 'string', 'max:255', 'unique:users,username']],
            'password' => ['required', Rules\Password::defaults()],
            'gdpr' => ['required', 'accepted'],

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
                'main_address.latitude' => ['nullable', 'numeric'],
                'main_address.longitude' => ['nullable', 'numeric'],
                'main_address.notes' => ['nullable', 'string', 'max:255'],
            ],

            'billing_address' => [
                'billing_address.address_line_1' => ['required', 'string', 'max:255'],
                'billing_address.address_line_2' => ['nullable', 'string', 'max:255'],
                'billing_address.city' => ['nullable', 'string', 'max:255'],
                'billing_address.state' => ['nullable', 'string', 'max:255'],
                'billing_address.postal_code' => ['nullable', 'string', 'max:255'],
                'billing_address.country_code' => ['nullable', 'exists:countries,code'],
                'billing_address.latitude' => ['nullable', 'numeric'],
                'billing_address.longitude' => ['nullable', 'numeric'],
                'billing_address.notes' => ['nullable', 'string', 'max:255'],
            ],

            'shipping_address' => [
                'shipping_address.address_line_1' => ['required', 'string', 'max:255'],
                'shipping_address.address_line_2' => ['nullable', 'string', 'max:255'],
                'shipping_address.city' => ['nullable', 'string', 'max:255'],
                'shipping_address.state' => ['nullable', 'string', 'max:255'],
                'shipping_address.postal_code' => ['nullable', 'string', 'max:255'],
                'shipping_address.country_code' => ['nullable', 'exists:countries,code'],
                'shipping_address.latitude' => ['nullable', 'numeric'],
                'shipping_address.longitude' => ['nullable', 'numeric'],
                'shipping_address.notes' => ['nullable', 'string', 'max:255'],
            ],
        ];
    }
}



class RegistrationProfile {
    private Collection $all_profiles;
    private Collection $selected_profiles;
    private $default_profile;

    public function __construct(Collection $profiles)
    {
        $this->all_profiles = $profiles;
        $this->default_profile = $this->getDefaultProfile($profiles);
    }

    public function select(Array $selection = []): RegistrationProfile
    {
        $this->selected_profiles = $this->getSelectedProfiles($this->all_profiles, $selection);
        return $this;
    }

    private function getDefaultProfile(Collection $profiles)
    {
        $profile = $profiles->firstWhere('name', 'default');
        if (!$profile) throw new \Exception('Registration profiles incomplete.');

        return $profile;
    }
    
    private function getSelectedProfiles(Collection $profiles, Array $selection = [])
    {
        return $profiles->whereIn('name', $selection);
    }

    public function getValidation(): Bool
    {
        return false;
    }

    public function getRules(Array $ruleset): Array
    {
        $computed_profiles = $this->selected_profiles->merge([$this->default_profile]);
        $rules = [];

        $fields = $computed_profiles->map(fn ($profile) => $profile['fields'])->flatten()->unique();

        $rules = $fields->map(fn ($field) => $ruleset[$field] ?? null);
        

        throw new \Exception(json_encode($rules));
        return [];
    }
}