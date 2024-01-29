<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $path = Media::dissectPath($request->path);
        
        if ($path->hasSubfolder)
        {
            $files = Media::where('src_path', $path->path)->first()->children()->get();
        }
        else
        {
            $files = Media::where('drive', $path->diskname)
                ->where('parent_id', null)
                ->orderByRaw("FIELD(mime_type , 'folder') DESC")
                ->orderBy('src_path', 'asc')
                ->get();
        }

        return response()->json($files);
    }



    public function move(Request $request)
    {
        $request->validate([
            'path' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
        ]);

        Media::firstWhere('src_path', $request->path)->move($request->destination);
    }



    public function rename(Request $request)
    {
        $request->validate([
            'path' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        Media::firstWhere('src_path', $request->path)->rename($request->name);
    }



    public function copy(Request $request)
    {
        $request->validate([
            'path' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
        ]);

        Media::firstWhere('src_path', $request->path)->copy($request->destination);
    }



    public function destroy(Request $request)
    {
        $request->validate([
            'path' => ['required', 'string', 'max:255'],
        ]);

        Media::firstWhere('src_path', $request->path)->delete();
    }
}
