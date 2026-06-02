<?php
namespace App\Models;

class PrioridadFalla extends Model
{
    protected static function table(): string { return 'prioridades_falla'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function allActive(): array
    {
        return self::findWhere("estado = 'activo'", [], 'nombre');
    }
}
