<?php
namespace App\Models;

class Calibracion extends Model
{
    protected static function table(): string { return 'calibraciones'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function findByEquipo(int $equipoId): array
    {
        return self::findWhere('equipo_id = ?', [$equipoId], 'fecha_proxima_calibracion');
    }

    public static function findVencidas(): array
    {
        return self::findWhere('fecha_proxima_calibracion < CURDATE() AND estado = ?', ['al_dia']);
    }

    public static function findProximas(int $dias = 30): array
    {
        return self::findWhere(
            'fecha_proxima_calibracion BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY) AND estado = ?',
            [$dias, 'al_dia'],
            'fecha_proxima_calibracion'
        );
    }
}
