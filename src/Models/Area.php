<?php
namespace App\Models;

class Area extends Model
{
    protected static function table(): string { return 'areas'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function findByLocalidad(int $localidadId): array
    {
        return self::findWhere('localidad_id = ? AND estado = ?', [$localidadId, 'activo'], 'nombre');
    }

    public static function allActive(): array
    {
        return self::findWhere("estado = 'activo'", [], 'nombre');
    }
}
