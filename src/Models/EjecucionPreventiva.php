<?php
namespace App\Models;

class EjecucionPreventiva extends Model
{
    protected static function table(): string { return 'ejecuciones_preventivas'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function findByOrden(int $ordenId): array
    {
        return self::findWhere('orden_preventiva_id = ?', [$ordenId], 'fecha_inicio_real');
    }

    public static function findActivaByMantenedor(int $mantenedorId): ?array
    {
        return self::findFirstWhere('mantenedor_id = ? AND fecha_fin_real IS NULL', [$mantenedorId]);
    }
}
