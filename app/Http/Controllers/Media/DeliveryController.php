<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Http\Resources\Media\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeliveryController extends Controller
{
    private bool $hasAccessToRequestedPath = false;



    public function __invoke(Request $request)
    {
        $this->hasAccessToRequestedPath = Media::canUser(auth('sanctum')->user(), Media::READ_ACCESS, $request->path);

        if (!$request->expectsJson()) {
            return $this->serve($request);
        }

        if ($request->expectsJson() && !$request->exact) {
            return $this->index($request);
        }

        if ($request->expectsJson() && $request->exact) {
            return $this->show($request);
        }
    }



    private function index(Request $request)
    {
        // Base query
        $query = Media::whereChildOfPath($request->path)->with('accesses');

        // Search
        if ($request->filter_search) {
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
        $query->whereVisibleToUser(auth('sanctum')->user(), $this->hasAccessToRequestedPath);

        // Return collection + pagination
        return MediaResource::collection($query->paginate($request->size ?? 20));
    }



    private function show(Request $request)
    {
        if (!$this->hasAccessToRequestedPath) return abort(403);

        return MediaResource::make(Media::findPathOrFail($request->path));
    }



    private function serve(Request $request)
    {
        if (!$this->hasAccessToRequestedPath) return abort(403);

        return response()->file(Storage::path(Media::findPathOrFail($request->path)->src_path));
    }
}
