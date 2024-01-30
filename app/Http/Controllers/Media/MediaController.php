<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\CopyMediaRequest;
use App\Http\Requests\Media\DestroyMediaRequest;
use App\Http\Requests\Media\MoveMediaRequest;
use App\Http\Requests\Media\RenameMediaRequest;
use App\Http\Resources\Media\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $path = Media::dissectPath($request->path);
        
        if ($path->hasSubfolder)
        {
            $media = Media::findPath($path->path)->children()->get();
        }
        else
        {
            $media = Media::where('drive', $path->diskname)
                ->where('parent_id', null)
                ->orderByRaw("FIELD(mime_type , 'folder') DESC")
                ->orderBy('src_path', 'asc')
                ->get();
        }

        return MediaResource::collection($media);
    }



    public function move(MoveMediaRequest $request)
    {
        Media::findPath($request->path)->move($request->destination);
    }



    public function rename(RenameMediaRequest $request)
    {
        Media::findPath($request->path)->rename($request->name);
    }



    public function copy(CopyMediaRequest $request)
    {
        Media::findPath($request->path)->copy($request->destination);
    }



    public function destroy(DestroyMediaRequest $request)
    {
        Media::findPath($request->path)->delete();
    }
}
