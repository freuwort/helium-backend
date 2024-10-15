<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\Form;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->notifications;
    }

    
    
    public function markRead(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|exists:notifications,id',
        ]);

        $request->user()->notifications()->whereIn('id', $request->items)->update(['read_at' => now()]);
    }



    public function markUnread(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'required|exists:notifications,id',
        ]);

        $request->user()->notifications()->whereIn('id', $request->items)->update(['read_at' => null]);
    }
}
