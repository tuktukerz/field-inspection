<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageCompressor
{
    public static function compressAndStore(
        UploadedFile $file,
        string $directory,
        string $disk = 'public',
        int $maxDimension = 1280,
        int $quality = 65,
    ): string {
        $imageData = file_get_contents($file->getRealPath());
        $source = @imagecreatefromstring($imageData);

        if (! $source) {
            return $file->store($directory, $disk);
        }

        $width = imagesx($source);
        $height = imagesy($source);

        if ($width > $maxDimension || $height > $maxDimension) {
            $ratio = min($maxDimension / $width, $maxDimension / $height);
            $newWidth = (int) ($width * $ratio);
            $newHeight = (int) ($height * $ratio);

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            $white = imagecolorallocate($resized, 255, 255, 255);
            imagefill($resized, 0, 0, $white);
            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($source);
            $source = $resized;
        }

        $filename = Str::random(40) . '.jpg';
        $path = trim($directory, '/') . '/' . $filename;

        ob_start();
        imagejpeg($source, null, $quality);
        $compressed = ob_get_clean();
        imagedestroy($source);

        Storage::disk($disk)->put($path, $compressed);

        return $path;
    }
}
