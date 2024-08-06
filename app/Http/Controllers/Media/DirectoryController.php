<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\CreateDirectoryRequest;
use App\Models\Media;

class DirectoryController extends Controller
{
    public function store(CreateDirectoryRequest $request)
    {
        $this->authorize('createDirectory', [Media::class, $request->validated('path')]);

        return response()->json([
            'directory' => Media::createDirectory($request->validated('path'), $request->validated('name')),
        ]);
    }
}
