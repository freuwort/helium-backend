<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\CopyMediaRequest;
use App\Http\Requests\Media\DestroyMediaRequest;
use App\Http\Requests\Media\MoveMediaRequest;
use App\Http\Requests\Media\RenameMediaRequest;
use App\Http\Requests\Media\ShareMediaRequest;
use App\Http\Resources\Media\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
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



    public function discovery(Request $request)
    {
        Media::discovery($request->path);
    }



    public function rename(RenameMediaRequest $request)
    {
        Media::findPathOrFail($request->path)->rename($request->name);
    }



    public function share(ShareMediaRequest $request)
    {
        $media = Media::findPathOrFail($request->path);
        
        $media->update([ 'inherit_access' => $request->inherit_access ]);
        
        $media->removeAllAccess();

        $media->addAccess(null, ['permission' => $request->public_access]);

        foreach ($request->access as $access)
        {
            $media->addAccess([
                'id' => $access['permissible_id'],
                'type' => $access['permissible_type'],
            ], ['permission' => $access['permission']]);
        }
    }



    public function move(MoveMediaRequest $request)
    {
        Media::moveMany($request->paths, $request->destination);
    }



    public function copy(CopyMediaRequest $request)
    {
        Media::copyMany($request->paths, $request->destination);
    }



    public function destroy(DestroyMediaRequest $request)
    {
        Media::deleteMany($request->paths);
    }
}
