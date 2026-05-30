<?php
namespace App\Models;

class TipoFalla extends Model
{
    protected static function table(): string { return 'tipos_falla'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function allActive(): array
    {
        return self::findWhere("estado = 'activo'", [], 'nombre');
    }
}
