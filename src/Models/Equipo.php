<?php
namespace App\Models;

class Equipo extends Model
{
    protected static function table(): string { return 'equipos'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function allActive(): array
    {
        return self::findWhere("estado = 'activo'", [], 'nombre');
    }

    public static function listWithFilters(string $family, int $categoriaId, int $zonaId, string $estado): array
    {
        $where = ['1 = 1'];
        $params = [];

        if ($family !== '') {
            $where[] = 'e.grupo_responsable = ?';
            $params[] = $family;
        }
        if ($categoriaId > 0) {
            $where[] = 'e.categoria_id = ?';
            $params[] = $categoriaId;
        }
        if ($zonaId > 0) {
            $where[] = 'e.zona_id = ?';
            $params[] = $zonaId;
        }
        if ($estado !== 'todos') {
            $where[] = 'e.estado = ?';
            $params[] = $estado;
        }

        return self::raw(
            'SELECT e.id, e.nombre, e.grupo_responsable AS familia, c.nombre AS categoria, z.nombre AS zona, e.estado, e.descripcion, e.numero_activo_fijo
             FROM equipos e
             LEFT JOIN categorias_equipo c ON c.id = e.categoria_id
             LEFT JOIN zonas z ON z.id = e.zona_id
             WHERE ' . implode(' AND ', $where) . '
             ORDER BY e.nombre ASC',
            $params
        );
    }

    public static function families(): array
    {
        $pdo = \App\Core\Database::connection();
        return $pdo->query(
            "SELECT DISTINCT grupo_responsable FROM equipos WHERE grupo_responsable IS NOT NULL AND TRIM(grupo_responsable) <> '' ORDER BY grupo_responsable"
        )->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function toggleStatus(int $id): ?string
    {
        $equipo = self::find($id);
        if (!$equipo) return null;
        $newStatus = $equipo['estado'] === 'activo' ? 'inactivo' : 'activo';
        self::update($id, ['estado' => $newStatus, 'actualizado_en' => date('Y-m-d H:i:s')]);
        return $newStatus;
    }
}
