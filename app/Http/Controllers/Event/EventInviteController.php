<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\CreateEventInviteRequest;
use App\Http\Requests\Event\DestroyManyEventInviteRequest;
use App\Http\Requests\Event\ImportEventInvitesRequest;
use App\Http\Requests\Event\UpdateEventInviteRequest;
use App\Http\Requests\SendTemplatedEmailRequest;
use App\Http\Resources\Event\BasicEventInviteResource;
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
        $query = $event->invites();

        // Search
        if ($request->filter_search)
        {
            $query->whereFuzzy(function ($query) use ($request) {
                $query
                    ->orWhereFuzzy('name', $request->filter_search)
                    ->orWhereFuzzy('email', $request->filter_search)
                    ->orWhereFuzzy('phone', $request->filter_search)
                    ->orWhereFuzzy('code', $request->filter_search);
            });
        }

        // Filter
        if ($request->filter_status)
        {
            $includesPending = in_array('pending', $request->filter_status);

            $query->where(function ($query) use ($request, $includesPending) {
                $query->whereIn('status', $request->filter_status);
                if ($includesPending) $query->orWhereNull('status');
            });
        }
        if ($request->filter_type)
        {
            $query->whereIn('type', $request->filter_type);
        }

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return EventInviteResource::collection($query->paginate($request->size ?? 20))
            ->additional(['keys' => $query->pluck('id')->toArray()])
            ->additional(['filter_values' => [
                'type' => $event->invites()->distinct('type')->pluck('type')->toArray(),
            ]]);
    }

    
    
    public function showBasic(Request $request)
    {
        return BasicEventInviteResource::make(EventInvite::where('code', $request->code)->firstOrFail());
    }


    
    public function show(Event $event, EventInvite $invite)
    {
        return EditorEventInviteResource::make($event->invites()->find($invite->id));
    }

    
    
    public function store(CreateEventInviteRequest $request, Event $event)
    {
        $invite = $event->invites()->create($request->model);

        return EditorEventInviteResource::make($invite);
    }



    public function import(ImportEventInvitesRequest $request, Event $event)
    {
        $this->authorize('create');

        foreach ($request->items as $item)
        {
            $event->invites()->create($item);
        }
    }

    
    
    public function update(UpdateEventInviteRequest $request, Event $event, EventInvite $invite)
    {
        $invite = $event->invites()->find($invite->id);

        $invite->update($request->model);

        return EditorEventInviteResource::make($invite);
    }

    public function claim(Request $request)
    {
        $invite = EventInvite::where('code', $request->code)->firstOrFail();

        $invite->update(['user_id' => auth()->user()->id]);

        return BasicEventInviteResource::make($invite);
    }

    public function updateStatus(Request $request)
    {
        $invite = EventInvite::where('code', $request->code)->firstOrFail();

        $invite->update(['status' => $request->status]);

        return BasicEventInviteResource::make($invite);
    }

    public function updateDetails(Request $request)
    {
        $invite = EventInvite::where('code', $request->code)->firstOrFail();

        $invite->submitForm($request->all());

        return BasicEventInviteResource::make($invite);
    }



    public function sendTemplatedEmail(SendTemplatedEmailRequest $request, Event $event)
    {
        // $this->authorize('deleteMany', [EventInvite::class, $request->ids]);

        $event->invites()->whereIn('id', $request->ids)->get()->each(function ($invite) use ($request) {
            $invite->sendTemplatedEmail([
                'cc' => $request->cc,
                'bcc' => $request->bcc,
                'subject' => $request->subject,
                'message' => $request->message,
                'attachments' => $request->attachments,
            ]);
        });
    }

    
    
    public function destroy(Event $event, EventInvite $invite)
    {
        $event->invites()->find($invite->id)->delete();
    }

    
    
    public function destroyMany(DestroyManyEventInviteRequest $request, Event $event)
    {
        $this->authorize('deleteMany', [EventInvite::class, $request->ids]);

        $event->invites()->whereIn('id', $request->ids)->delete();
    }
}
