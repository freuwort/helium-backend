<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeliveryController extends Controller
{
    public function __invoke(Request $request)
    {
        $model = Media::where('src_path', $request->path)->firstOrFail();
        
        return response()->file(Storage::path($model->src_path));
    }
}
