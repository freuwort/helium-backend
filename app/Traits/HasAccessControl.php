<?php

namespace App\Traits;

use App\Models\Access;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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
    public function scopeWhereModelHasAccess($query, Model $model, Array $permissions)
    {
        // $inheritAccessColumn = $this->inherit_access_column;
        $inheritAccessColumn = 'inherit_access';
        // $defaultAccess = $this->getDefaultAccess();
        $defaultAccess = config('filesystems.disk_default_access');

        return $query->where(function ($query) use ($model, $permissions, $defaultAccess, $inheritAccessColumn) {
            return $query
            // Where inherit access and has parent > go deeper
            ->where($inheritAccessColumn, true)
            ->whereNotNull('parent_id')
            ->where(function ($query) use ($model, $permissions) {
                return $query
                ->whereHas('parent', function ($query) use ($model, $permissions) {
                    // return $query->whereModelHasAccess($model, $permissions);
                    return $query->where('name', 'spezifikationen');
                });
            });

            // Where inherit access and no parent > select default access
            // ->orWhere(function ($query) use ($permissions, $defaultAccess, $inheritAccessColumn) {
            //     return $query
            //     ->where($inheritAccessColumn, true)
            //     ->whereNull('parent_id')
            //     ->where(function ($query) use ($permissions, $defaultAccess) {
            //         // Return if public access
            //         if (in_array($defaultAccess['any']['guest'], $permissions)) return $query;

            //         return $query;
                    
            //     });
            // });
            // Where custom access > select
            // ->orWhere(function ($query) use ($permissions, $inheritAccessColumn) {
            //     $query
            //     ->where($inheritAccessColumn, false)
            //     ->where(function ($query) use ($permissions) {
            //         $query
            //         ->whereHas('accesses', function ($query) use ($permissions) {
            //             return $query
            //             ->whereNull('type')
            //             ->whereNull('permissible_id')
            //             ->whereNull('permissible_type')
            //             ->whereIn('permission', [...$permissions, null]);
            //         });
            //     });
            // });
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

    public function checkIf(Model $model)
    {
        return $this->checkIfAny([$model]);
    }

    public function checkIfGuest()
    {
        return $this->checkIfAny([]);
    }

    public function checkIfAny(array|Collection $models)
    {
        return new AccessCheck($this->computeAccess(), $models);
    }
}

class AccessCheck
{
    private $access;
    private $models;

    public function __construct($access, $models)
    {
        $this->access = $access;
        $this->models = $models;

        return $this;
    }

    public function can($permission)
    {
        return $this->canAny([$permission]);
    }

    public function canAny(array $permissions)
    {
        if (!count($this->models) && isset($this->access['any']['guest']) && in_array($this->access['any']['guest'], $permissions)) return true;

        foreach ($this->models as $model)
        {
            $group = (string) $model::class;
            $id = (string) $model->getKey();

            if (isset($this->access[$group][$id]) && in_array($this->access[$group][$id], $permissions)) return true;
        }

        return false;
    }
}