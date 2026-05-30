<?php
namespace App\Services;

use App\Core\Session;
use App\Models\OrdenCorrectiva;
use App\Models\FotoCorrectiva;
use App\Models\EjecucionChecklistItem;
use App\Models\LogAuditoria;

class CorrectivaService
{
    public static function checkAccess(): bool
    {
        $allowedRoles = ['Supervisor', 'Planificador/Programador'];
        $rolActual = Session::get('rol_nombre', '');
        return AuthService::isAdmin()
            || in_array($rolActual, $allowedRoles, true)
            || AuthService::hasPermission('correctivas', 'ver');
    }

    public static function changeState(int $id, string $newState, string $otpCode): array
    {
        $orden = OrdenCorrectiva::find($id);
        if (!$orden) {
            return ['ok' => false, 'error' => 'Orden no encontrada.'];
        }

        $transitions = OrdenCorrectiva::allowedTransitions($orden['estado']);
        if (!in_array($newState, $transitions, true)) {
            return ['ok' => false, 'error' => 'Transición no permitida.'];
        }

        $now = date('Y-m-d H:i:s');
        $updates = ['estado' => $newState, 'actualizada_en' => $now];

        match ($newState) {
            'en_progreso' => $updates['fecha_inicio_ejecucion'] = $now,
            'completada' => $updates['fecha_completada'] = $now,
            'cerrada' => $updates['fecha_cierre'] = $now,
            default => null,
        };

        OrdenCorrectiva::update($id, $updates);
        return ['ok' => true, 'message' => 'Estado cambiado a "' . $newState . '" correctamente.'];
    }

    public static function uploadPhoto(int $ordenId, array $file): array
    {
        $count = FotoCorrectiva::countByCorrectiva($ordenId);
        if ($count >= 3) {
            return ['ok' => false, 'error' => 'Máximo 3 fotos por orden.'];
        }

        $allowedTypes = ['image/jpeg', 'image/png'];
        if (!in_array($file['type'], $allowedTypes, true)) {
            return ['ok' => false, 'error' => 'Solo se permiten JPG y PNG.'];
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return ['ok' => false, 'error' => 'La imagen no debe superar 5 MB.'];
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/assets/uploads/fotos-fallas';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = $file['type'] === 'image/png' ? 'png' : 'jpg';
        $filename = 'falla_' . $ordenId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $destPath = $uploadDir . '/' . $filename;

        if ($ext === 'jpg') {
            $img = @imagecreatefromjpeg($file['tmp_name']);
            if ($img) {
                $maxDim = 1920;
                $w = imagesx($img);
                $h = imagesy($img);
                if ($w > $maxDim || $h > $maxDim) {
                    $ratio = min($maxDim / $w, $maxDim / $h);
                    $newW = (int) ($w * $ratio);
                    $newH = (int) ($h * $ratio);
                    $resized = imagecreatetruecolor($newW, $newH);
                    imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
                    imagejpeg($resized, $destPath, 80);
                    imagedestroy($resized);
                } else {
                    imagejpeg($img, $destPath, 80);
                }
                imagedestroy($img);
            } else {
                move_uploaded_file($file['tmp_name'], $destPath);
            }
        } else {
            $img = @imagecreatefrompng($file['tmp_name']);
            if ($img) {
                $maxDim = 1920;
                $w = imagesx($img);
                $h = imagesy($img);
                if ($w > $maxDim || $h > $maxDim) {
                    $ratio = min($maxDim / $w, $maxDim / $h);
                    $newW = (int) ($w * $ratio);
                    $newH = (int) ($h * $ratio);
                    $resized = imagecreatetruecolor($newW, $newH);
                    imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
                    imagepng($resized, $destPath, 7);
                    imagedestroy($resized);
                } else {
                    imagepng($img, $destPath, 7);
                }
                imagedestroy($img);
            } else {
                move_uploaded_file($file['tmp_name'], $destPath);
            }
        }

        FotoCorrectiva::insert([
            'orden_correctiva_id' => $ordenId,
            'ruta_archivo' => $filename,
            'subido_por_usuario_id' => Session::get('usuario_id'),
            'creado_en' => date('Y-m-d H:i:s'),
        ]);

        return ['ok' => true, 'message' => 'Foto subida correctamente.'];
    }

    public static function deletePhoto(int $fotoId, int $ordenId): array
    {
        $foto = FotoCorrectiva::find($fotoId);
        if (!$foto || (int) $foto['orden_correctiva_id'] !== $ordenId) {
            return ['ok' => false, 'error' => 'Foto no encontrada.'];
        }

        $filePath = dirname(__DIR__, 2) . '/public/assets/uploads/fotos-fallas/' . $foto['ruta_archivo'];
        if (is_file($filePath)) {
            unlink($filePath);
        }

        FotoCorrectiva::delete($fotoId);
        return ['ok' => true, 'message' => 'Foto eliminada.'];
    }

    public static function toggleChecklistItem(int $correctivaId, int $itemId, string $valor): void
    {
        EjecucionChecklistItem::upsert($correctivaId, $itemId, $valor);
    }

    public static function logAudit(int $correctivaId, string $accion, string $detalle = ''): void
    {
        LogAuditoria::insert([
            'orden_correctiva_id' => $correctivaId,
            'usuario_id' => Session::get('usuario_id'),
            'accion' => $accion,
            'detalle' => $detalle,
            'creado_en' => date('Y-m-d H:i:s'),
        ]);
    }
}
