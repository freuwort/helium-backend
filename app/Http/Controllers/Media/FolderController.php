<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'path' => ['required', 'string', 'max:255'],
        ]);

        $folder = Media::createFolder($request->path, $request->name);

        return response()->json([
            'folder' => $folder
        ]);
    }
}
