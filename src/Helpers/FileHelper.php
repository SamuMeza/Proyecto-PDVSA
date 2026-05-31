<?php
namespace App\Helpers;

class FileHelper
{
    public static function upload(array $file, string $targetDir, array $allowedTypes = []): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Error al subir el archivo.'];
        }

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedTypes) && !in_array($ext, $allowedTypes)) {
            return ['ok' => false, 'error' => 'Tipo de archivo no permitido.'];
        }

        $filename = uniqid('file_') . '.' . $ext;
        $destPath = rtrim($targetDir, '/') . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            return ['ok' => false, 'error' => 'No se pudo guardar el archivo.'];
        }

        return [
            'ok' => true,
            'filename' => $filename,
            'path' => $destPath,
            'size' => $file['size'],
            'ext' => $ext,
        ];
    }

    public static function delete(string $path): bool
    {
        if (is_file($path)) {
            return unlink($path);
        }
        return false;
    }

    public static function sizeFormat(int $bytes, int $decimals = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, $decimals) . ' ' . $units[$i];
    }

    public static function mimeType(string $path): string
    {
        if (!is_file($path)) return '';
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $path);
        finfo_close($finfo);
        return $mime;
    }

    public static function sanitizeFilename(string $filename): string
    {
        $filename = preg_replace('/[^\w\-\.\s]/', '', $filename);
        $filename = preg_replace('/\s+/', '_', $filename);
        return trim($filename, '._-');
    }
}
