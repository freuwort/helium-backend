<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\DestroyManyRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\Role\BasicRoleResource;
use App\Http\Resources\Role\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }



    public function indexBasic(Request $request)
    {
        // Check if user can view models
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

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return RoleResource::collection($query->paginate($request->size ?? 20));
    }

    
    
    public function show(Role $role)
    {
        return RoleResource::make($role);
    }

    
    
    public function store(CreateRoleRequest $request)
    {
        $role = Role::create([...$request->validated(), 'guard_name' => 'web']);
        $role->syncPermissions($request->permissions);

        return RoleResource::make($role);
    }

    
    
    public function update(UpdateRoleRequest $request, Role $role)
    {
        $role->update($request->validated());
        $role->syncPermissions($request->permissions);

        return RoleResource::make($role);
    }

    
    
    public function destroy(Role $role)
    {
        $role->delete();
    }



    public function destroyMany(DestroyManyRoleRequest $request)
    {
        $this->authorize('deleteMany', Role::class);

        Role::whereIn('id', $request->ids)->delete();
    }
}
