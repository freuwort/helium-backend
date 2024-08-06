<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Http\Resources\Media\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeliveryController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!Media::canUser(auth()->user(), ['read', 'write', 'admin'], $request->path)) return abort(403);

        if (!$request->expectsJson())                       return $this->serve($request);
        if ($request->expectsJson() && !$request->exact)    return $this->index($request);
        if ($request->expectsJson() && $request->exact)     return $this->show($request);
    }



    private function index(Request $request)
    {
        // Base query
        $query = Media::whereChildOfPath($request->path)->with('accesses');

        // Search
        if ($request->filter_search)
        {
            $query
            ->whereFuzzy(function ($query) use ($request) {
                $query
                ->orWhereFuzzy('src_path', $request->filter_search)
                ->orWhereFuzzy('name', $request->filter_search);
            });
        }

        // Filter

        // Sort
        $query->orderByDefault();

        // Access Management
        $user = $request->user();
        $permissions = ['read', 'write', 'admin'];
        $hasParentAccess = Media::canUser($user, $permissions, $request->path);

        $query->whereModelHasAccess($user, $permissions, $hasParentAccess);

        // Return collection + pagination
        return MediaResource::collection($query->paginate($request->size ?? 20));
    }



    private function show(Request $request)
    {
        return MediaResource::make(Media::findPathOrFail($request->path));
    }



    private function serve(Request $request)
    {
        return response()->file(Storage::path(Media::findPathOrFail($request->path)->src_path));
    }
}
