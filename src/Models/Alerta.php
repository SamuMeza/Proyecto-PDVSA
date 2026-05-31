<?php
namespace App\Models;

class Alerta extends Model
{
    protected static function table(): string { return 'alertas'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function pendientesByUser(int $usuarioId): array
    {
        return self::findWhere('usuario_destino_id = ? AND leida = ?', [$usuarioId, false], 'fecha_generacion DESC');
    }

    public static function countPendientes(int $usuarioId): int
    {
        return self::count('usuario_destino_id = ? AND leida = ?', [$usuarioId, false]);
    }

    public static function marcarLeida(int $id): void
    {
        self::update($id, ['leida' => true, 'fecha_lectura' => date('Y-m-d H:i:s')]);
    }

    public static function marcarTodasLeidas(int $usuarioId): void
    {
        self::execute(
            'UPDATE alertas SET leida = ?, fecha_lectura = ? WHERE usuario_destino_id = ? AND leida = ?',
            [true, date('Y-m-d H:i:s'), $usuarioId, false]
        );
    }
}
