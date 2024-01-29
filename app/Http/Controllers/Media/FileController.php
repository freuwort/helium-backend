<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'files' => ['array', 'max:1000'],
            'files.*' => ['file', 'mimetypes:font/*,image/*,text/*,audio/*,video/*,application/*'],
            'path' => ['required', 'string', 'max:255'],
        ]);

        $files = $request->file('files');
        $uploadedFiles = [];

        foreach ($files as $file)
        {
            $uploadedFiles[] = Media::upload($request->path, $file);
        }

        return response()->json([
            'files' => $uploadedFiles
        ]);
    }
}
