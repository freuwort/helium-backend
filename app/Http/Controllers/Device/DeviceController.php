<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Http\Requests\Device\CreateDeviceRequest;
use App\Http\Requests\Device\DestroyManyDeviceRequest;
use App\Http\Requests\Device\UpdateDeviceRequest;
use App\Http\Resources\Device\EditorDeviceResource;
use App\Http\Resources\Device\DeviceResource;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Device::class);

        // Base query
        $query = Device::with(['owner'])->whereActive();

        // Search
        if ($request->filter_search)
        {
            $query->whereFuzzy(function ($query) use ($request) {
                $query
                    ->orWhereFuzzy('name', $request->filter_search)
                    ->orWhereFuzzy('group', $request->filter_search)
                    ->orWhereFuzzy('os_platform', $request->filter_search)
                    ->orWhereFuzzy('os_arch', $request->filter_search)
                    ->orWhereFuzzy('os_release', $request->filter_search);
            });
        }

        // Filter

        // Sort
        $field = $request->sort_field ?? 'created_at';
        $order = $request->sort_order ?? 'desc';

        $query->orderBy($field, $order);

        // Return collection + pagination
        return DeviceResource::collection($query->paginate($request->size ?? 20));
    }

    
    
    public function show(Device $device)
    {
        $this->authorize('view', $device);

        return DeviceResource::make($device);
    }

    
    
    public function store(Request $request)
    {
        $this->authorize('create', Device::class);

        $device = Device::firstOrCreate([ 'type' => null ], []);

        if ($device->shouldRegeneratePin()) {
            $device->regeneratePin();
        }

        return response()->json([
            'pin' => $device->pin,
            'valid_until' => $device->valid_until,
        ]);
    }



    // Because the device is looked up by type,
    // we don't need to include a device ID.
    // We also dont need a policy for this method
    // as the device is sending the request.
    public function activate(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'string'],
            'type' => ['required', 'string', 'in:' . implode(',', Device::TYPES)],
            'name' => ['required', 'string'],
            'os_platform' => ['nullable', 'string'],
            'os_arch' => ['nullable', 'string'],
            'os_release' => ['nullable', 'string'],
            'app_version' => ['nullable', 'string'],
        ]);

        $device = Device::wherePending()->firstOrFail();
        
        if (!$device->validatePin($request->pin)) {
            abort(403, 'Invalid PIN');
        }

        $token = $device->activateAs($request->validated());

        return response()->json([
            'id' => $device->id,
            'token' => $token,
        ]);
    }

    
    
    // public function update(UpdateDeviceRequest $request, Device $device)
    // {
    //     $this->authorize('update', $device);

    //     $device->update($request->validated('model'));

    //     return DeviceResource::make($device->fresh());
    // }

    
    
    public function destroy(Device $device)
    {
        $this->authorize('delete', $device);

        $device->delete();
    }

    
    
    public function destroyMany(Request $request)
    {
        $request->validate(['ids.*' => ['required', 'integer', 'exists:devices,id']]);
        
        $devices = Device::whereIn('id', $request->ids);
        
        $this->authorize('deleteMany', [Device::class, $devices->get()]);
        
        $devices->delete();
    }
}
