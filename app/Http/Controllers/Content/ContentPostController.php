<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContentPost\CreateContentPostRequest;
use App\Http\Requests\ContentPost\DestroyManyContentPostRequest;
use App\Http\Requests\ContentPost\UpdateContentPostRequest;
use App\Http\Requests\ContentPost\UpdateReviewStatusOnContentPostRequest;
use App\Http\Resources\ContentPost\ContentPostGroupResource;
use App\Http\Resources\ContentPost\EditorContentPostGroupResource;
use App\Models\ContentPostGroup;
use App\Models\ContentSpace;
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
        // $field = $request->sort_field ?? 'post.created_at';
        $order = $request->sort_order ?? 'desc';

        // $query->orderBy($field, $order);

        // Return collection + pagination
        return ContentPostGroupResource::collection($query->paginate($request->size ?? 20))
        ->additional(['keys' => $query->pluck('id')->toArray()])
        ->additional(['filter_values' => [
            'space' => ContentSpace::select(['id', 'name'])->get()->toArray(),
        ]]);
    }

    
    
    public function show(ContentPostGroup $postGroup)
    {
        return EditorContentPostGroupResource::make($postGroup);
    }

    
    
    public function store(CreateContentPostRequest $request)
    {
        $postGroup = ContentPostGroup::create($request->validated('model'));
        $postGroup->posts()->create($request->validated('draft'));

        return EditorContentPostGroupResource::make($postGroup);
    }

    
    
    public function update(UpdateContentPostRequest $request, ContentPostGroup $postGroup)
    {
        $postGroup->draft()->update($request->validated('draft'));

        return EditorContentPostGroupResource::make($postGroup);
    }



    public function updateReviewStatus(UpdateReviewStatusOnContentPostRequest $request, ContentPostGroup $postGroup)
    {
        $postGroup->draft()->update($request->validated('draft'));

        return EditorContentPostGroupResource::make($postGroup);
    }



    public function approveDraft(Request $request, ContentPostGroup $postGroup)
    {
        $postGroup->approveDraft();

        return EditorContentPostGroupResource::make($postGroup);
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
