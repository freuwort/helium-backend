<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContentSpace\CreateContentSpaceRequest;
use App\Http\Requests\ContentSpace\DestroyManyContentSpaceRequest;
use App\Http\Requests\ContentSpace\UpdateContentSpaceRequest;
use App\Http\Resources\ContentSpace\ContentSpaceResource;
use App\Http\Resources\ContentSpace\EditorContentSpaceResource;
use App\Http\Resources\User\BasicUserResource;
use App\Models\ContentSpace;
use App\Models\User;
use Illuminate\Http\Request;

class ContentSpaceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ContentSpace::class, 'content-space');
    }



    public function index(Request $request)
    {
        // Base query
        $query = ContentSpace::with(['owner', 'accesses']);

        // Search
        if ($request->filter_search)
        {
            $query
            ->whereFuzzy(function ($query) use ($request) {
                $query
                ->orWhereFuzzy('name', $request->filter_search);
            });
        }

        // Filter
        if ($request->filter_parent)
        {
            $includesNull = in_array(null, $request->filter_parent);
            
            $query->where(function ($query) use ($request, $includesNull) {
                $query->whereIn('parent_id', $request->filter_parent);
                if ($includesNull) $query->orWhereNull('parent_id');
            });
        }

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return ContentSpaceResource::collection($query->paginate($request->size ?? 20))
        ->additional(['keys' => $query->pluck('id')->toArray()])
        ->additional(['filter_values' => [
            'parent' => ContentSpace::distinct('name')->pluck('name')->toArray(),
        ]]);
    }

    
    
    public function show(ContentSpace $space)
    {
        return EditorContentSpaceResource::make($space);
    }

    
    
    public function store(CreateContentSpaceRequest $request)
    {
        $space = ContentSpace::create($request->validated('model'));

        return EditorContentSpaceResource::make($space);
    }

    
    
    public function update(UpdateContentSpaceRequest $request, ContentSpace $space)
    {
        $space->update($request->validated('model'));

        return EditorContentSpaceResource::make($space);
    }

    
    
    public function destroy(ContentSpace $space)
    {
        $space->delete();
    }

    
    
    public function destroyMany(DestroyManyContentSpaceRequest $request)
    {
        $this->authorize('deleteMany', [ContentSpace::class, $request->ids]);

        ContentSpace::whereIn('id', $request->ids)->delete();
    }
}
