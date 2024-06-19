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
        $user = $request->user() ?? auth()->user() ?? null;
        $permissions = ['read', 'write', 'admin'];

        // If the request is a json request, return a collection of media
        if ($request->expectsJson())
        {
            return MediaResource::collection(
                Media::query()
                    ->whereChildOfPath($request->path)
                    ->orderByDefault()
                    ->get()
                    ->filter(fn ($media) => $media->userCanAny($user, $permissions))
            );
        }

        // Otherwise return the file itself
        $media = Media::where('src_path', $request->path)->firstOrFail();

        if ($media->userCanAny($user, $permissions) === false) abort(403);
        
        return response()->file(Storage::path($media->src_path));
    }
}
