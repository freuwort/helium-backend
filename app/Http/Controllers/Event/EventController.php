<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\CreateEventRequest;
use App\Http\Requests\Event\DestroyManyEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Resources\Event\EditorEventResource;
use App\Http\Resources\Event\EventResource;
use App\Models\Address;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Event::class, 'event');
    }



    public function index(Request $request)
    {
        // Base query
        $query = Event::query();

        // Search
        if ($request->filter_search)
        {
            $query->whereFuzzy(function ($query) use ($request) {
                $query
                    ->orWhereFuzzy('name', $request->filter_search)
                    ->orWhereFuzzy('slug', $request->filter_search)
                    ->orWhereFuzzy('description', $request->filter_search);
            });
        }

        // Filter

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return EventResource::collection($query->paginate($request->size ?? 20));
    }

    
    
    public function show(Event $event)
    {
        return EditorEventResource::make($event);
    }

    
    
    public function store(CreateEventRequest $request)
    {
        $event = Event::create($request->model);

        $event->syncMany(Address::class, $request->addresses);

        return EditorEventResource::make($event);
    }

    
    
    public function update(UpdateEventRequest $request, Event $event)
    {
        $event->update($request->model);

        $event->syncMany(Address::class, $request->addresses);
        $event->media()->sync($request->media);

        return EditorEventResource::make($event);
    }

    
    
    public function destroy(Event $event)
    {
        $event->delete();
    }

    
    
    public function destroyMany(DestroyManyEventRequest $request)
    {
        $this->authorize('deleteMany', [Event::class, $request->ids]);

        Event::whereIn('id', $request->ids)->delete();
    }
}
