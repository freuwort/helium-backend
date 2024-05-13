<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'path' => ['required', 'string', 'max:255'],
        ]);

        $directory = Media::createDirectory($request->path, $request->name);

        return response()->json([
            'directory' => $directory
        ]);
    }
}
