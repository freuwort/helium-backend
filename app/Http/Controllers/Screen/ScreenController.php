<?php

namespace App\Http\Controllers\Screen;

use App\Http\Controllers\Controller;
use App\Http\Requests\Screen\CreateScreenRequest;
use App\Http\Requests\Screen\DestroyManyScreenRequest;
use App\Http\Requests\Screen\UpdateScreenRequest;
use App\Http\Resources\Screen\EditorScreenResource;
use App\Http\Resources\Screen\ScreenResource;
use App\Models\Screen;
use Illuminate\Http\Request;

class ScreenController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Screen::class);

        // Base query
        $query = Screen::with([]);

        // Search
        if ($request->filter_search)
        {
            $query->whereFuzzy(function ($query) use ($request) {
                $query
                    ->orWhereFuzzy('name', $request->filter_search);
            });
        }

        // Filter

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return ScreenResource::collection($query->paginate($request->size ?? 20));
    }

    
    
    public function show(Screen $screen)
    {
        $this->authorize('view', $screen);

        return EditorScreenResource::make($screen);
    }

    
    
    public function store(CreateScreenRequest $request)
    {
        $this->authorize('create', Screen::class);

        $screen = Screen::create($request->validated('model'));

        return EditorScreenResource::make($screen);
    }

    
    
    public function update(UpdateScreenRequest $request, Screen $screen)
    {
        $this->authorize('update', $screen);

        $screen->update($request->validated('model'));

        return EditorScreenResource::make($screen->fresh());
    }

    
    
    public function destroy(Screen $screen)
    {
        $this->authorize('delete', $screen);

        $screen->delete();
    }

    
    
    public function destroyMany(DestroyManyScreenRequest $request)
    {
        $screens = Screen::whereIn('id', $request->validated('ids'));

        $this->authorize('deleteMany', [Screen::class, $screens->get()]);

        $screens->delete();
    }
}
