<?php

namespace App\Http\Controllers\Permission;

use App\Classes\Permissions\Permissions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'data' => Permissions::GROUPED_PERMISSIONS,
        ]);
    }
}
