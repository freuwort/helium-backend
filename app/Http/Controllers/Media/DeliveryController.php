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
        if ($request->expectsJson() && !$request->exact) return $this->index($request);
        if ($request->expectsJson() && $request->exact) return $this->show($request);
        
        return $this->serve($request);
    }



    public function index(Request $request)
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
        $parentAccess = Media::checkIfUser(['src_path', $request->path], $user, $permissions);

        $query->whereModelHasAccess($user, $permissions, $parentAccess);

        // Return collection + pagination
        return MediaResource::collection($query->paginate($request->size ?? 20));
    }



    public function show(Request $request)
    {
        $user = $request->user();
        $permissions = ['read', 'write', 'admin'];

        $media = Media::where('src_path', $request->path)->firstOrFail();

        if (!Media::checkIfUser($media, $user, $permissions)) abort(403);

        return MediaResource::make($media);
    }



    public function serve(Request $request)
    {
        $user = $request->user();
        $permissions = ['read', 'write', 'admin'];

        $media = Media::where('src_path', $request->path)->firstOrFail();

        if (!Media::checkIfUser($media, $user, $permissions)) abort(403);
        
        return response()->file(Storage::path($media->src_path));
    }
}
