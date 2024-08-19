<?php

namespace App\Http\Controllers\Screen;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScreenDevice\CreateScreenDeviceRequest;
use App\Http\Requests\ScreenDevice\DestroyManyScreenDeviceRequest;
use App\Http\Requests\ScreenDevice\UpdateScreenDeviceRequest;
use App\Http\Resources\ScreenDevice\EditorScreenDeviceResource;
use App\Http\Resources\ScreenDevice\ScreenDeviceResource;
use App\Models\ScreenDevice;
use Illuminate\Http\Request;

class ScreenDeviceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ScreenDevice::class);

        // Base query
        $query = ScreenDevice::with(['owner']);

        // Search
        if ($request->filter_search)
        {
            $query->whereFuzzy(function ($query) use ($request) {
                $query
                    ->orWhereFuzzy('name', $request->filter_search)
                    ->orWhereFuzzy('group', $request->filter_search);
            });
        }

        // Filter

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return ScreenDeviceResource::collection($query->paginate($request->size ?? 20));
    }

    
    
    public function show(ScreenDevice $device)
    {
        $this->authorize('view', $device);

        return EditorScreenDeviceResource::make($device);
    }

    
    
    public function store(CreateScreenDeviceRequest $request)
    {
        $this->authorize('create', ScreenDevice::class);

        $device = ScreenDevice::create($request->validated('model'));
        $device->address()->create($request->validated('address'));
        $device->playlists()->sync($request->playlists); // Deliberately no validated() method

        return EditorScreenDeviceResource::make($device);
    }

    
    
    public function update(UpdateScreenDeviceRequest $request, ScreenDevice $device)
    {
        $this->authorize('update', $device);

        $device->update($request->validated('model'));
        $device->address()->update($request->validated('address'));
        $device->playlists()->sync($request->playlists); // Deliberately no validated() method

        return EditorScreenDeviceResource::make($device->fresh());
    }

    
    
    public function destroy(ScreenDevice $device)
    {
        $this->authorize('delete', $device);

        $device->delete();
    }

    
    
    public function destroyMany(DestroyManyScreenDeviceRequest $request)
    {
        $devices = ScreenDevice::whereIn('id', $request->validated('ids'));

        $this->authorize('deleteMany', [ScreenDevice::class, $devices->get()]);

        $devices->delete();
    }
}
