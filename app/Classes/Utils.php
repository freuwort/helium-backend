<?php

namespace App\Classes;

class Utils
{
    public static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3)
        {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        ];
    }

    public static function rgbToHex(array $rgb): string
    {
        return sprintf("#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2]);
    }

    public static function interpolateColors(string $color1, string $color2, int $maxStep): array
    {
        // Konvertiere die beiden Hex-Farben in RGB
        $rgb1 = self::hexToRgb($color1);
        $rgb2 = self::hexToRgb($color2);
    
        // Array für interpolierte Farben
        $interpolatedColors = [];
    
        // Berechne interpolierte Farben
        for ($step = 0; $step <= $maxStep; $step++)
        {
            $ratio = $step / $maxStep;
            
            $interpolatedColor = [
                (int)($rgb1[0] + ($rgb2[0] - $rgb1[0]) * $ratio),
                (int)($rgb1[1] + ($rgb2[1] - $rgb1[1]) * $ratio),
                (int)($rgb1[2] + ($rgb2[2] - $rgb1[2]) * $ratio)
            ];
    
            $interpolatedColors[] = self::rgbToHex($interpolatedColor);
        }
    
        return $interpolatedColors;
    }
}