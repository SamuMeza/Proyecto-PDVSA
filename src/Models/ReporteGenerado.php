<?php
namespace App\Models;

class ReporteGenerado extends Model
{
    protected static function table(): string { return 'reportes_generados'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function findByUser(int $usuarioId): array
    {
        return self::findWhere('generado_por_usuario_id = ?', [$usuarioId], 'fecha_generacion DESC');
    }

    public static function findByTipo(string $tipo): array
    {
        return self::findWhere('tipo_reporte = ?', [$tipo], 'fecha_generacion DESC');
    }
}
