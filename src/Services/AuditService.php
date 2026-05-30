<?php
namespace App\Services;

class AuditService
{
    public static function log(int $ordenId, int $usuarioId, string $accion, string $detalle = ''): void
    {
        \App\Models\LogAuditoria::insert([
            'orden_correctiva_id' => $ordenId,
            'usuario_id' => $usuarioId,
            'accion' => $accion,
            'detalle' => $detalle,
            'creado_en' => date('Y-m-d H:i:s'),
        ]);
    }

    public static function getByCorrectiva(int $ordenId): array
    {
        return \App\Models\LogAuditoria::findByCorrectiva($ordenId);
    }
}
