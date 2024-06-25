<?php

namespace App\Traits;

use App\Classes\Permissions\Permissions;
use App\Models\Access;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait HasAccessControl
{
    protected $inherit_access_column = 'inherit_access';



    public function accesses()
    {
        return $this->morphMany(Access::class, 'accessable');
    }

    public function parent()
    {
        return null;
    }



    private function getInheritAccess(){}

    public function getDefaultAccess()
    {
        return ['any' => ['guest' => null]];
    }



    // START: Scopes
    public function scopeWhereModelHasAccess($query, Model|null $model, array $permissions, $access)
    {
        // $inheritAccessColumn = $this->inherit_access_column;
        $inheritAccessColumn = 'inherit_access';

        // Check for super admin
        if ($model instanceof User && $model->can([Permissions::SYSTEM_ADMIN, Permissions::SYSTEM_SUPER_ADMIN])) return $query;

        // Main query
        return $query->where(function ($query) use ($model, $permissions, $access, $inheritAccessColumn) {
            return $query
            // Where inherit access
            ->where(function ($query) use ($model, $permissions, $access, $inheritAccessColumn) {
                return $query
                ->where($inheritAccessColumn, true)
                ->when($access, function ($query) {
                    return $query;
                }, function ($query) use ($inheritAccessColumn) {
                    return $query->where($inheritAccessColumn, false);
                });
            })

            // Where owner
            ->orWhere(function ($query) use ($model) {
                return $query
                ->when(!!$model, function ($query) use ($model) {
                    return $query
                    ->where('owner_id', $model->getKey())
                    ->where('owner_type', $model::class);
                });
            })

            // Where custom access
            ->orWhere(function ($query) use ($model, $permissions, $inheritAccessColumn) {
                return $query
                ->where($inheritAccessColumn, false)
                ->where(function ($query) use ($model, $permissions) {
                    
                    // Public access
                    $query
                    ->whereHas('accesses', function ($query) use ($permissions) {
                        return $query
                        ->whereNull('type')
                        ->whereNull('permissible_id')
                        ->whereNull('permissible_type')
                        ->whereIn('permission', [...$permissions, null]);
                    });

                    // Early return when no model
                    if (!$model) return $query;

                    // Specific access
                    $query
                    ->orWhereHas('accesses', function ($query) use ($model, $permissions) {
                        return $query
                        ->whereNull('type')
                        ->where('permissible_id', $model->getKey())
                        ->where('permissible_type', $model::class)
                        ->whereIn('permission', [...$permissions, null]);
                    });
                });
            });
        });
    }
    // END: Scopes
    


    public function addAccess(Model|array|null $model, array $options = [])
    {
        $model_id = is_array($model) ? $model['id'] : null;
        $model_type = is_array($model) ? $model['type'] : null;

        if ($model instanceof Model)
        {
            $model_id = $model->getKey();
            $model_type = $model::class;
        }

        $this->accesses()->updateOrCreate([
            'permissible_id' => $model_id,
            'permissible_type' => $model_type,
            'type' => $options['type'] ?? null,
        ], [
            'permission' => $options['permission'] ?? null,
        ]);
    }



    public function removeAccess(Model $model)
    {
        $this->accesses()->where('permissible_id', $model->getKey())->where('permissible_type', $model::class)->delete();
    }
    
    public function removeAllAccess(string|null $type = null)
    {
        $this->accesses()->where('type', $type)->delete();
    }



    public function computeAccess()
    {
        $defaultAccess = collect($this->getDefaultAccess());

        // Recursively compute access if access is inherited
        if ($this->inherit_access === true)
        {
            return $this->parent ? $this->parent->computeAccess() : $defaultAccess;
        }

        $publicAccess = collect(['any' => ['guest' => $this
            ->accesses()
            ->whereNull('type')
            ->whereNull('permissible_id')
            ->whereNull('permissible_type')
            ->first()
            ->permission ?? null
        ]]);

        $specificAccess = $this
            ->accesses()
            ->select('type', 'permissible_id', 'permissible_type', 'permission')
            ->whereNull('type')
            ->whereNotNull('permissible_id')
            ->whereNotNull('permissible_type')
            ->get()
            ->groupBy('permissible_type')
            ->map(fn ($group) => $group->pluck('permission', 'permissible_id'));

        return $defaultAccess
            ->merge($publicAccess)
            ->merge($specificAccess);
    }



    public static function checkIf(Model|array|string $model, Model|Collection|array $models, array|string $permissions): bool
    {
        // Correct $model type
        if (is_array($model)) $model = self::where(...$model)->firstOrFail();
        if ($model instanceof string) $model = self::findOrFail($model);

        // Correct $models type
        if ($models instanceof Collection) $models = $models->all();
        if ($models instanceof Model) $models = [$models];

        // Correct $permissions type
        if ($permissions instanceof string) $permissions = [$permissions];

        // Create access check
        $check = new AccessCheck($model->computeAccess(), $models);

        // Check access
        return $check->can($permissions);
    }

    public static function checkIfUser(Model|array|string $model, User|null $user, array|string $permissions): bool
    {
        // Check for guest
        if (!$user) return self::checkIfGuest($model, $permissions);

        // Check for super admin
        if ($user->can([Permissions::SYSTEM_ADMIN, Permissions::SYSTEM_SUPER_ADMIN])) return true;

        // Check for user and their roles
        return self::checkIf($model, [$user, ...$user->roles()->get()], $permissions);

    }

    public static function checkIfGuest(Model|array|string $model, array|string $permissions): bool
    {
        return self::checkIf($model, [], $permissions);
    }
}



class AccessCheck {
    private $access;
    private $models;

    public function __construct($access, $models) {
        $this->access = $access;
        $this->models = $models;

        return $this;
    }

    public function can(array|string $permissions) {
        // Correct $permissions type
        if ($permissions instanceof string) $permissions = [$permissions];

        if (!count($this->models) && isset($this->access['any']['guest']) && in_array($this->access['any']['guest'], $permissions)) return true;

        foreach ($this->models as $model) {
            $group = (string) $model::class;
            $id = (string) $model->getKey();

            if (isset($this->access[$group][$id]) && in_array($this->access[$group][$id], $permissions)) return true;
        }

        return false;
    }
}