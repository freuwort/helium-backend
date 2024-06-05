<?php

namespace App\Http\Controllers\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Event\CreateEventInviteRequest;
use App\Http\Requests\Event\DestroyManyEventInviteRequest;
use App\Http\Requests\Event\ImportEventInvitesRequest;
use App\Http\Requests\Event\UpdateEventInviteRequest;
use App\Http\Requests\SendTemplatedEmailRequest;
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
        return EventInviteResource::collection($query->paginate($request->size ?? 20))
            ->additional(['keys' => $query->pluck($query->getModel()->getKeyName())->toArray()]);
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
