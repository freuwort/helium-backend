<?php

namespace App\Classes\Auth;

use Illuminate\Support\Collection;

class RegistrationValidator
{
    private Collection $profiles;
    private Collection $selectedProfiles;
    private Collection $computedProfiles;
    private Array $defaultProfile;

    /**
     * Load the registration profiles.
     * 
     * @param Array $profiles
     * @return RegistrationValidator
     */
    public function loadProfiles(Array $profiles): RegistrationValidator
    {
        $profiles = collect($profiles);

        $this->profiles = $profiles;
        $this->defaultProfile = $profiles->firstWhere('name', 'default');

        if (!$this->defaultProfile) {
            throw new \Exception('Registration profiles incomplete.');
        }

        return $this;
    }

    /**
     * Select the profiles.
     * 
     * @param Array<string> $selection
     * @return RegistrationValidator
     */
    public function selectProfiles(Array $selection = []): RegistrationValidator
    {
        $this->selectedProfiles = $this->profiles->whereIn('name', $selection);
        $this->computedProfiles = $this->selectedProfiles->merge([$this->defaultProfile]);

        return $this;
    }
    
    /**
     * Validate the profiles.
     *
     * @return Object
     */
    public function validate()
    {
        return (Object) [
            'isValid' => $this->getIsValid(),
            'autoEnable' => $this->getAutoEnable(),
            'fields' => $this->getFields(),
            'roles' => $this->getRoles(),
        ];
    }

    /**
     * Determine if the selected profiles are valid.
     *
     * @return Bool
     */
    private function getIsValid(): bool
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
    private function getAutoEnable(): Bool
    {
        return $this->computedProfiles->filter(fn ($profile) => $profile['auto_enable'])->isNotEmpty();
    }

    /**
     * Get the computed profiles fields.
     *
     * @return Array<string>
     */
    private function getFields(): Array
    {
        return $this->computedProfiles->map(fn ($profile) => $profile['fields'])->flatten()->unique()->toArray();
    }

    /**
     * Get the computed profiles roles.
     *
     * @return Array<string>
     */
    private function getRoles(): Array
    {
        return $this->computedProfiles->map(fn ($profile) => $profile['auto_assign_roles'])->flatten()->unique()->toArray();
    }
}