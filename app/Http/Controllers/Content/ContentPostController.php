<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContentSpace\CreateContentSpaceRequest;
use App\Http\Requests\ContentSpace\DestroyManyContentSpaceRequest;
use App\Http\Requests\ContentSpace\UpdateContentSpaceRequest;
use App\Http\Resources\ContentSpace\ContentSpaceResource;
use App\Http\Resources\ContentSpace\EditorContentSpaceResource;
use App\Models\ContentPostGroup;
use Illuminate\Http\Request;

class ContentPostController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ContentPostGroup::class, 'content-post-group');
    }



    public function index(Request $request)
    {
        // Base query
        $query = ContentPostGroup::with(['owner', 'accesses']);

        // Search
        if ($request->filter_search)
        {
            $query
            ->whereFuzzy(function ($query) use ($request) {
                $query
                ->orWhereFuzzy('post.name', $request->filter_search);
            });
        }

        // Filter

        // Sort
        $field = $request->sort_field ?? 'post.created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return ContentPostResource::collection($query->paginate($request->size ?? 20))
        ->additional(['keys' => $query->pluck('id')->toArray()]);
    }

    
    
    public function show(ContentPostGroup $postGroup)
    {
        return EditorContentPostResource::make($postGroup);
    }

    
    
    public function store(CreateContentPostRequest $request)
    {
        $postGroup = ContentPostGroup::create($request->validated('model'));

        return EditorContentPostResource::make($postGroup);
    }

    
    
    public function update(UpdateContentPostRequest $request, ContentPostGroup $postGroup)
    {
        $postGroup->update($request->validated('model'));

        return EditorContentPostResource::make($postGroup);
    }

    
    
    public function destroy(ContentPostGroup $postGroup)
    {
        $postGroup->delete();
    }

    
    
    public function destroyMany(DestroyManyContentPostRequest $request)
    {
        $this->authorize('deleteMany', [ContentPostGroup::class, $request->ids]);

        ContentPostGroup::whereIn('id', $request->ids)->delete();
    }
}
