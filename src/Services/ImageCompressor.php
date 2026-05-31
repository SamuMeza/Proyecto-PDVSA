<?php
namespace App\Services;

class ImageCompressor
{
    public static int $maxWidth = 1920;
    public static int $maxHeight = 1080;
    public static int $quality = 80;

    public static function compress(string $sourcePath, ?string $destPath = null): array
    {
        if (!is_file($sourcePath)) {
            return ['ok' => false, 'error' => 'Archivo no encontrado.'];
        }

        $info = getimagesize($sourcePath);
        if ($info === false) {
            return ['ok' => false, 'error' => 'No es una imagen válida.'];
        }

        [$width, $height, $type] = $info;
        $destPath ??= $sourcePath;

        $srcImg = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => imagecreatefrompng($sourcePath),
            IMAGETYPE_GIF => imagecreatefromgif($sourcePath),
            default => null,
        };

        if ($srcImg === null) {
            return ['ok' => false, 'error' => 'Formato no soportado.'];
        }

        $ratio = min(self::$maxWidth / $width, self::$maxHeight / $height, 1);
        $newWidth = (int) round($width * $ratio);
        $newHeight = (int) round($height * $ratio);

        if ($ratio < 1) {
            $dstImg = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        } else {
            $dstImg = $srcImg;
        }

        imagejpeg($dstImg, $destPath, self::$quality);
        imagedestroy($srcImg);
        if ($ratio < 1) imagedestroy($dstImg);

        return [
            'ok' => true,
            'path' => $destPath,
            'original_size' => filesize($sourcePath),
            'new_size' => filesize($destPath),
        ];
    }
}
