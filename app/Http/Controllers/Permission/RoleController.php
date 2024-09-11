<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\DestroyManyRoleRequest;
use App\Http\Requests\Role\ImportRolesRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\Role\BasicRoleResource;
use App\Http\Resources\Role\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function indexBasic(Request $request)
    {
        $this->authorize('basicViewAny', Role::class);
        
        // Base query
        $query = Role::query();

        // Search
        if ($request->filter_search)
        {
            $query->whereFuzzy(function ($query) use ($request) {
                $query
                    ->orWhereFuzzy('name', $request->filter_search);
            });
        }

        // Filter
        if ($request->filter_exclude)
        {
            $query->whereNotIn('id', $request->filter_exclude);
        }

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return BasicRoleResource::collection($query->paginate($request->size ?? 20));
    }



    public function index(Request $request)
    {
        $this->authorize('viewAny', Role::class);

        // Base query
        $query = Role::query();

        // Search
        if ($request->filter_search)
        {
            $query->whereFuzzy(function ($query) use ($request) {
                $query
                    ->orWhereFuzzy('name', $request->filter_search);
            });
        }

        // Filter
        if ($request->filter_exclude)
        {
            $query->whereNotIn('id', $request->filter_exclude);
        }

        if ($request->filter_permissions)
        {
            $query->whereHas('permissions', function ($query) use ($request) {
                $query->whereIn('name', $request->filter_permissions);
            });
        }

        if ($request->filter_permission_levels)
        {
            $query->where(function ($query) use ($request) {
                if (in_array('admin', $request->filter_permission_levels)) $query->orWhere(fn ($query) => $query->whereIsAdmin());
                if (in_array('elevated', $request->filter_permission_levels)) $query->orWhere(fn ($query) => $query->whereHasElevatedPermissions());
            });
        }

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return RoleResource::collection($query->paginate($request->size ?? 20))
        ->additional(['keys' => $query->pluck('id')->toArray()])
        ->additional(['filter_values' => [
            'permission' => Permission::all()->pluck('name')->toArray(),
        ]]);
    }

    
    
    public function show(Role $role)
    {
        $this->authorize('view', $role);

        return RoleResource::make($role);
    }

    
    
    public function store(CreateRoleRequest $request)
    {
        $this->authorize('create', [Role::class, $request->permissions]);

        $role = Role::create($request->validated());
        $role->syncPermissions($request->permissions);

        return RoleResource::make($role);
    }



    public function import(ImportRolesRequest $request)
    {
        $this->authorize('import', [Role::class, $request->items]);

        foreach ($request->items as $item)
        {
            $role = Role::create($item);
            $role->syncPermissions($item['permissions']);
        }
    }

    
    
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $this->authorize('update', [$role, $request->permissions]);

        $role->update($request->validated());
        $role->syncPermissions($request->permissions);

        return RoleResource::make($role);
    }

    
    
    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);
        
        $role->delete();
    }



    public function destroyMany(DestroyManyRoleRequest $request)
    {
        $roles = Role::whereIn('id', $request->ids);

        $this->authorize('deleteMany', [Role::class, $roles->get()]);

        $roles->delete();
    }
}
