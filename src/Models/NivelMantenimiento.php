<?php
namespace App\Models;

class NivelMantenimiento extends Model
{
    protected static function table(): string { return 'niveles_mantenimiento'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function allActive(): array
    {
        return self::findWhere("estado = 'activo'", [], 'nombre');
    }
}
