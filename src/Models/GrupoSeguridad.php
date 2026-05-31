<?php
namespace App\Models;

class GrupoSeguridad extends Model
{
    protected static function table(): string { return 'grupos_seguridad'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function allActive(): array
    {
        return self::findWhere("estado = 'activo'", [], 'nombre');
    }

    public static function existsActive(int $id): bool
    {
        return self::findFirstWhere('id = ? AND estado = ?', [$id, 'activo']) !== null;
    }
}
