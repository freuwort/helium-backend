<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\CreateEventInviteRequest;
use App\Http\Requests\Event\CreateEventRequest;
use App\Http\Requests\Event\DestroyManyEventInviteRequest;
use App\Http\Requests\Event\DestroyManyEventRequest;
use App\Http\Requests\Event\ImportEventInvitesRequest;
use App\Http\Requests\Event\UpdateEventInviteRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Resources\Event\EditorEventInviteResource;
use App\Http\Resources\Event\EventInviteResource;
use App\Models\Event;
use App\Models\EventInvite;
use Illuminate\Http\Request;

class EventInviteController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(EventInvite::class, 'event-invite');
    }



    public function index(Request $request, Event $event)
    {
        // Base query
        $query = EventInvite::query();

        // Search
        if ($request->filter_search)
        {
            $query->whereFuzzy(function ($query) use ($request) {
                $query
                    ->orWhereFuzzy('email', $request->filter_search)
                    ->orWhereFuzzy('phone', $request->filter_search)
                    ->orWhereFuzzy('code', $request->filter_search);
            });
        }

        // Filter

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return EventInviteResource::collection($query->paginate($request->size ?? 20));
    }

    
    
    public function show(Event $event, EventInvite $invite)
    {
        return EditorEventInviteResource::make($invite);
    }

    
    
    public function store(CreateEventInviteRequest $request, Event $event)
    {
        $invite = EventInvite::create($request->model);

        return EditorEventInviteResource::make($invite);
    }



    public function import(ImportEventInvitesRequest $request, Event $event)
    {
        $this->authorize('create');

        foreach ($request->items as $item)
        {
            EventInvite::create([ ...$item, 'event_id' => $event->id ]);
        }
    }

    
    
    public function update(UpdateEventInviteRequest $request, Event $event, EventInvite $invite)
    {
        $invite->update($request->model);

        return EditorEventInviteResource::make($invite);
    }

    
    
    public function destroy(Event $event, EventInvite $invite)
    {
        $invite->delete();
    }

    
    
    public function destroyMany(DestroyManyEventInviteRequest $request,  Event $event)
    {
        $this->authorize('deleteMany', [EventInvite::class, $request->ids]);

        EventInvite::whereIn('id', $request->ids)->delete();
    }
}
