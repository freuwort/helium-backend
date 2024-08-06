<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\UploadMediaRequest;
use App\Models\Media;

class FileController extends Controller
{
    public function upload(UploadMediaRequest $request)
    {
        $this->authorize('upload', [Media::class, $request->validated('path')]);

        $files = $request->file('files');
        $uploadedFiles = [];

        foreach ($files as $file)
        {
            $uploadedFiles[] = Media::upload($request->validated('path'), $file);
        }

        return response()->json([
            'files' => $uploadedFiles
        ]);
    }
}
