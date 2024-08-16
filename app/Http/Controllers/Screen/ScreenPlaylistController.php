<?php

namespace App\Http\Controllers\Screen;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScreenPlaylist\CreateScreenPlaylistRequest;
use App\Http\Requests\ScreenPlaylist\DestroyManyScreenPlaylistRequest;
use App\Http\Requests\ScreenPlaylist\UpdateScreenPlaylistRequest;
use App\Http\Resources\ScreenPlaylist\EditorScreenPlaylistResource;
use App\Http\Resources\ScreenPlaylist\ScreenPlaylistResource;
use App\Models\ScreenPlaylist;
use Illuminate\Http\Request;

class ScreenPlaylistController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ScreenPlaylist::class);

        // Base query
        $query = ScreenPlaylist::with([]);

        // Search
        if ($request->filter_search)
        {
            $query->whereFuzzy(function ($query) use ($request) {
                $query
                    ->orWhereFuzzy('name', $request->filter_search)
                    ->orWhereFuzzy('type', $request->filter_search);
            });
        }

        // Filter

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return ScreenPlaylistResource::collection($query->paginate($request->size ?? 20));
    }

    
    
    public function show(ScreenPlaylist $playlist)
    {
        $this->authorize('view', $playlist);

        return EditorScreenPlaylistResource::make($playlist);
    }

    
    
    public function store(CreateScreenPlaylistRequest $request)
    {
        $this->authorize('create', ScreenPlaylist::class);

        $playlist = ScreenPlaylist::create($request->validated('model'));

        return EditorScreenPlaylistResource::make($playlist);
    }

    
    
    public function update(UpdateScreenPlaylistRequest $request, ScreenPlaylist $playlist)
    {
        $this->authorize('update', $playlist);

        $playlist->update($request->validated('model'));

        return EditorScreenPlaylistResource::make($playlist->fresh());
    }

    
    
    public function destroy(ScreenPlaylist $playlist)
    {
        $this->authorize('delete', $playlist);

        $playlist->delete();
    }

    
    
    public function destroyMany(DestroyManyScreenPlaylistRequest $request)
    {
        $playlists = ScreenPlaylist::whereIn('id', $request->validated('ids'));

        $this->authorize('deleteMany', [ScreenPlaylist::class, $playlists->get()]);

        $playlists->delete();
    }
}
