<?php

namespace App\Classes\Auth;

use Illuminate\Support\Collection;

class ConfigError
{
    static function create(Int $code = 200, String $message = ''): Object
    {
        return (Object) [
            'code' => $code,
            'message' => $message,
        ];
    }
}

class RegistrationConfig
{
    private Collection $profiles;
    private Collection $selectedProfiles;
    private Collection $computedProfiles;
    private Array|null $defaultProfile;

    /**
     * Create a new RegistrationConfig instance.
     *  
     * @param Array $profiles
     * @return RegistrationConfig
     */
    public function __construct(Array $profiles = [])
    {
        $this->load($profiles);

        return $this;
    }

    /**
     * Load the registration profiles.
     * 
     * @param Array $profiles
     * @return RegistrationConfig
     */
    public function load(Array $profiles): RegistrationConfig
    {
        $profiles = collect($profiles);

        $this->profiles = $profiles;
        $this->defaultProfile = $profiles->firstWhere('name', 'default');

        return $this;
    }

    /**
     * Select the profiles.
     * 
     * @param Array<string> $selection
     * @return RegistrationConfig
     */
    public function select(Array $selection = []): RegistrationConfig
    {
        $this->selectedProfiles = $this->profiles->whereIn('name', $selection);
        $this->computedProfiles = $this->selectedProfiles->merge([$this->defaultProfile]);

        return $this;
    }
    
    /**
     * Validate the profiles.
     *
     * @return ?Object
     */
    public function getOrElse($callback): ?Object
    {
        if (!$this->hasValidProfiles()) {
            return $callback(ConfigError::create(500, 'Registration is currently unavailable due to a configuration error. Please contact the site administrator.'));
        }

        if (!$this->hasValidSelection()) {
            return $callback(ConfigError::create(422, 'The selection of profiles is not valid.'));
        }

        return (Object) [
            'autoEnable' => $this->autoEnable(),
            'fields' => $this->fields(),
            'roles' => $this->roles(),
        ];
    }



    private function hasValidProfiles(): Bool    
    {
        if ($this->profiles->isEmpty()) return false;
        if (!$this->defaultProfile) return false;

        return true;
    }

    /**
     * Determine if the selected profiles are valid.
     *
     * @return Bool
     */
    private function hasValidSelection(): Bool
    {
        function getCommon(array $arrays): array
        {
            if (empty($arrays)) {
                return [];
            }

            $commonItems = array_intersect(...$arrays);
            return array_values($commonItems);
        }

        // Step 1: Check if there are any common groups across selected profiles
        $groupLists = array_map(fn($profile) => $profile['groups'] ?? [], $this->selectedProfiles->toArray());
        $commonGroups = getCommon($groupLists);

        // Step 2: Determine if 'useCommonGroups' condition is met
        $useCommonGroups = $this->selectedProfiles->some(fn($profile) => !empty($profile['groups']));

        // Step 3: Apply validation based on 'useCommonGroups' condition
        if (!$useCommonGroups) {
            $emptyGroupsCount = $this->selectedProfiles->filter(fn($profile) => empty($profile['groups']))->count();
            return $emptyGroupsCount <= 1;
        }

        return count($commonGroups) > 0;
    }

    /**
     * Determine if the computed profiles shoud auto enable users.
     *
     * @return Bool
     */
    private function autoEnable(): Bool
    {
        return $this->computedProfiles->filter(fn ($profile) => $profile['auto_enable'])->isNotEmpty();
    }

    /**
     * Get the computed profiles fields.
     *
     * @return Array<string>
     */
    private function fields(): Array
    {
        return $this->computedProfiles->map(fn ($profile) => $profile['fields'])->flatten()->unique()->toArray();
    }

    /**
     * Get the computed profiles roles.
     *
     * @return Array<string>
     */
    private function roles(): Array
    {
        return $this->computedProfiles->map(fn ($profile) => $profile['auto_assign_roles'])->flatten()->unique()->toArray();
    }
}