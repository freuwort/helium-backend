<?php

namespace App\Classes\Media;

use App\Classes\Utils;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Laravel\Facades\Image;

class MediaConversion
{
    private static function toThumbnail(ImageInterface $image): string
    {
        return $image
        ->pad(300, 300, '#00000000', 'center')
        ->toWebp(quality: 50)
        ->toDataUri();
    }

    public static function rasterImageToThumbnail(string $path): ?string
    {
        $input = storage_path("app/$path");

        $image = Image::read($input);

        return self::toThumbnail($image);
    }

    public static function vectorToThumbnail(string $path): ?string
    {
        $input = storage_path("app/$path");

        $image = Image::read($input);

        return self::toThumbnail($image);
    }

    public static function audioToThumbnail(string $path): ?string
    {
        $input = storage_path("app/$path");
        $shell_input = escapeshellarg($input);

        $hash = hash_file('sha256', $input);

        $output = storage_path("app/temp/$hash.wav");
        $shell_output = escapeshellarg($output);

        shell_exec( "ffmpeg -y -i $shell_input -vn -acodec pcm_s16le -ar 8000 -ac 1 $shell_output");



        $amplitudes = [];

        $file = fopen($output, 'rb');

        while (!feof($file))
        {
            $bytes = fread($file, 2);
        
            // Break if no bytes are left
            if (strlen($bytes) < 2) break;
            
            // Unpack bytes into integer
            $amplitude = unpack('s', $bytes)[1];
            
            // Add amplitude to array
            $amplitudes[] = $amplitude;
        }

        fclose($file);

        // Remove temp file
        unlink($output);



        $sampleSize = 300;

        // Normalize amplitudes
        $amplitudes = array_map(fn ($amplitude) => min(abs($amplitude), 32768) / 32768 * 3, $amplitudes);

        // Combine amplitudes into chunks
        $amplitudes = array_chunk($amplitudes, ceil(count($amplitudes) / $sampleSize));

        // Average chunks
        $amplitudes = array_map(fn ($chunk) => min(round((int) array_sum($chunk) / count($chunk) * 100), 100), $amplitudes);
        


        $width = 300;
        $height = 300;
        $colors = Utils::interpolateColors('#ff00ff', '#f59e0b', $sampleSize);
        $image = Image::read(public_path('default/thumbnail_background_audio.png'))
        ->pad($width, $height, '#00000000', 'center');
        
        foreach ($amplitudes as $key => $amplitude)
        {
            $amplitude = min(($amplitude + 2), $height);
            $x = $key * $width / $sampleSize;
            $y1 = $height / 2 - $amplitude / 2;
            $y2 = $height / 2 + $amplitude / 2;
            $color = $colors[$key];

            $image->drawLine(function ($line) use ($x, $y1, $y2, $color) {
                $line->from($x, $y1);
                $line->to($x, $y2);
                $line->color($color);
                $line->width(1);
            });
        }

        return self::toThumbnail($image);
    }

    public static function videoToThumbnail(string $path): ?string
    {
        $input = storage_path("app/$path");
        $shell_input = escapeshellarg($input);

        $hash = hash_file('sha256', $input);

        $output = storage_path("app/temp/$hash.jpg");
        $shell_output = escapeshellarg($output);

        shell_exec("ffmpeg -y -i $shell_input -ss 00:00:01.000 -update 1 -vframes 1 $shell_output");

        $image = Image::read($output)
        ->pad(300, 300, '#00000000', 'center')
        ->place(public_path('default/thumbnail_foreground_video.png'), 'center', 0, 0, 100);

        // Remove temp file
        unlink($output);

        return self::toThumbnail($image);
    }

    public static function pdfToThumbnail(string $path): ?string
    {
        $input = storage_path("app/$path");
        $shell_input = escapeshellarg($input);

        $hash = hash_file('sha256', $input);

        $output = storage_path("app/temp/$hash");
        $shell_output = escapeshellarg($output);
        $suffix = '.png';

        shell_exec("pdftoppm $shell_input $shell_output -png -f 1 -singlefile");

        $image = Image::read($output.$suffix);

        // Remove temp file
        unlink($output.$suffix);

        return self::toThumbnail($image);
    }
}