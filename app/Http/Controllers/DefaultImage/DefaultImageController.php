<?php

namespace App\Http\Controllers\DefaultImage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;

class DefaultImageController extends Controller
{
    public function __invoke(Request $request)
    {
        if (in_array($request->type, ['profile_image', 'logo', 'avatar'])) return $this->profileImage($request);
        if (in_array($request->type, ['profile_banner', 'banner'])) return $this->profileBanner($request);
        
        return abort(404);
    }



    private static function initials(string $name, int $length = 2): string
    {
        $name = trim(strtoupper($name));
        $words = explode(' ', $name);
        
        if (count($words) < $length) return substr($name, 0, $length);

        $initials = '';
        
        foreach ($words as $word)
        {
            $initials .= $word[0];
        }

        return substr($initials, 0, $length);
    }

    private static function color(string $name): string
    {
        $colors = [
            '#22c55e',
            '#f59e0b',
            '#f97316',
            '#f43f5e',
            '#a855f7',
            '#6366f1',
            '#3b82f6',
            '#334155',
            '#4ade80',
            '#fbbf24',
            '#fb923c',
            '#fb7185',
            '#c084fc',
            '#818cf8',
            '#60a5fa',
            '#64748b',
        ];

        return $colors[abs(crc32(md5($name)) % count($colors))];
    }



    public function profileImage(Request $request)
    {
        $initials = self::initials($request->seed ?? ':D', 2);
        $color = self::color($request->seed ?? '');
        $size = 96;

        $image = Image::create($size, $size);
        $image->fill($color);
        $image->text($initials, $size/2*0.95, $size/2, function ($font) use ($size) {
            $font->filename(public_path('fonts/Inter-Medium.ttf'));
            $font->size($size/2.5);
            $font->color('#ffffff');
            $font->align('center');
            $font->valign('middle');
        });
        
        return response($image->toJpeg(), 200, [ 'Content-Type' => 'image/jpeg' ]);
    }



    public function profileBanner(Request $request)
    {
        return response()->file(public_path('/default/banner.png'));
    }
}
