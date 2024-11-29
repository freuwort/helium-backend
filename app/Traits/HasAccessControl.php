<?php

namespace App\Traits;

use App\Classes\Permissions\Permissions;
use App\Models\Access;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait HasAccessControl
{
    // START: Relationships
    public function accesses()
    {
        return $this->morphMany(Access::class, 'accessable');
    }

    public function parent()
    {
        return null;
    }

    public function children()
    {
        return null;
    }
    // END: Relationships



    // START: Scopes
    public function scopeWhereVisibleToUser($query, User|null $user, bool $hasParentAccess)
    {
        // Check for admin
        if ($user instanceof User && $user->can(Permissions::ADMIN_PERMISSIONS)) {
            return $query;
        }

        // Main query
        return $query->where(function ($query) use ($user, $hasParentAccess) {

            // Where inherit access
            $query->where(function ($query) use ($hasParentAccess) {
                $query
                ->where('inherit_access', true)
                ->when($hasParentAccess, function ($query) {
                    return $query;
                }, function ($query) {
                    return $query->where('inherit_access', false);
                });
            });

            // Where owner
            if ($user instanceof User) {
                $query->orWherePolymorphic('owner', $user);
            }

            // Where custom access
            $query->orWhere(function ($query) use ($user) {
                $query
                ->where('inherit_access', false)
                ->where(function ($query) use ($user) {
                    
                    // Public access
                    $query->whereHas('accesses', function ($query) {
                        $query
                        ->wherePolymorphicNull('permissible')
                        ->whereIn('permission', ['read', 'write', 'admin']);
                    });

                    // Early return if no usre
                    if (!$user instanceof User) {
                        return $query;
                    }

                    // Specific access
                    $query->orWhereHas('accesses', function ($query) use ($user) {
                        $query
                        ->wherePolymorphicIn('permissible', [$user, ...$user->roles()->get()])
                        ->whereIn('permission', ['read', 'write', 'admin']);
                    });
                });
            });
        });
    }
    // END: Scopes



    // START: Access control
    public static function defaultAccess(Model|null $accessable): Collection
    {
        return collect(['any' => ['guest' => null]]);
    }

    public static function computeAccessVia($accessable): Collection
    {
        // TODO: allow for custom find column
        if (is_integer($accessable)) $accessable = self::find($accessable);
        if (is_string($accessable)) $accessable = self::find($accessable);

        return self::computeAccessViaModel($accessable);
    }

    public static function computeAccessViaModel(Model|null $accessable): Collection
    {
        $defaultAccess = self::defaultAccess($accessable);

        if (!$accessable) return $defaultAccess;

        // Recursively compute access if access is inherited
        if ($accessable->inherit_access === true)
        {
            return $accessable->parent ? self::computeAccessViaModel($accessable->parent) : $defaultAccess;
        }

        $publicAccess = collect(['any' => ['guest' => $accessable
            ->accesses()
            ->whereNull('type')
            ->whereNull('permissible_id')
            ->whereNull('permissible_type')
            ->first()
            ->permission ?? null
        ]]);

        $specificAccess = $accessable
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

    public static function can(Model|Collection|array $permissibles, array $permissions, $accessable): bool
    {
        // Correct $permissibles type
        if ($permissibles instanceof Collection) $permissibles = $permissibles->all();
        if ($permissibles instanceof Model) $permissibles = [$permissibles];
        
        // Get computed access
        $access = self::computeAccessVia($accessable);

        // Guest check
        if (isset($access['any']['guest']) && in_array($access['any']['guest'], $permissions)) return true;

        // Check each model
        foreach ($permissibles as $permissible) {
            $group = (string) $permissible::class;
            $id = (string) $permissible->getKey();

            // Check model access
            if (isset($access[$group][$id]) && in_array($access[$group][$id], $permissions)) return true;
        }

        // No match
        return false;
    }

    public static function canUser(?User $user, array $permissions, $accessable): bool
    {
        // Check for admin
        if ($user && $user->can(Permissions::ADMIN_PERMISSIONS)) return true;

        // Get permissibles
       $permissibles = $user ? [$user, ...$user->roles()->get()] : [];

        // Check permissibles
        return self::can($permissibles, $permissions, $accessable);

    }
    // END: Access control
    


    // START: Access methods
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
    // END: Access methods
}