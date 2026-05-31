<?php
namespace App\Models;

class Instalacion extends Model
{
    protected static function table(): string { return 'instalaciones'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function findByArea(int $areaId): array
    {
        return self::findWhere('area_id = ? AND estado = ?', [$areaId, 'activo'], 'nombre');
    }

    public static function allActive(): array
    {
        return self::findWhere("estado = 'activo'", [], 'nombre');
    }
}
