<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    // Multi File Upload
    public function upload(Request $request)
    {
        $request->validate([
            'files' => ['array', 'max:1000'],
            'files.*' => ['file', 'mimetypes:font/*,image/*,text/*,audio/*,video/*,application/*'],
        ]);
        
        $files = $request->file('files');
        $uploadedFiles = [];

        foreach ($files as $file)
        {
            $uploadedFiles[] = Media::upload($file, 'public');
        }

        return response()->json([
            'files' => $uploadedFiles
        ], 201);
    }
}
