<?php
namespace App\Models;

class OrdenPreventiva extends Model
{
    protected static function table(): string { return 'ordenes_preventivas'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function validStates(): array
    {
        return ['planificada', 'en_curso', 'cerrada', 'suspendida'];
    }

    public static function allowedTransitions(string $currentState): array
    {
        return match ($currentState) {
            'planificada' => ['en_curso', 'suspendida'],
            'en_curso' => ['cerrada', 'suspendida'],
            'cerrada' => [],
            'suspendida' => ['planificada'],
            default => [],
        };
    }

    public static function listWithFilters(int $equipoId, string $estado, string $fechaDesde, string $fechaHasta): array
    {
        $where = ['1 = 1'];
        $params = [];

        if ($equipoId > 0) {
            $where[] = 'op.equipo_id = ?';
            $params[] = $equipoId;
        }
        if ($estado !== 'todos') {
            $where[] = 'op.estado = ?';
            $params[] = $estado;
        }
        if ($fechaDesde !== '') {
            $where[] = 'op.fecha_planificada >= ?';
            $params[] = $fechaDesde;
        }
        if ($fechaHasta !== '') {
            $where[] = 'op.fecha_planificada <= ?';
            $params[] = $fechaHasta;
        }

        return self::raw(
            'SELECT op.*, e.nombre AS equipo_nombre, e.numero_activo_fijo, u.nombre_completo AS planificador_nombre
             FROM ordenes_preventivas op
             LEFT JOIN equipos e ON e.id = op.equipo_id
             LEFT JOIN usuarios u ON u.id = op.planificador_id
             WHERE ' . implode(' AND ', $where) . '
             ORDER BY op.fecha_planificada DESC, op.hora_inicio ASC',
            $params
        );
    }

    public static function findWithDetails(int $id): ?array
    {
        return self::rawOne(
            'SELECT op.*, e.nombre AS equipo_nombre, e.numero_activo_fijo,
                    nm.nombre AS nivel_nombre, u.nombre_completo AS planificador_nombre,
                    s.nombre_completo AS supervisor_nombre, m.nombre_completo AS mantenedor_nombre
             FROM ordenes_preventivas op
             LEFT JOIN equipos e ON e.id = op.equipo_id
             LEFT JOIN niveles_mantenimiento nm ON nm.id = op.nivel_mantenimiento_id
             LEFT JOIN usuarios u ON u.id = op.planificador_id
             LEFT JOIN usuarios s ON s.id = op.supervisor_asigno_id
             LEFT JOIN usuarios m ON m.id = op.mantenedor_id
             WHERE op.id = ? LIMIT 1',
            [$id]
        );
    }

    public static function getOtp(int $id): ?string
    {
        $row = self::rawOne('SELECT codigo_otp_validacion FROM ordenes_preventivas WHERE id = ?', [$id]);
        return $row ? $row['codigo_otp_validacion'] : null;
    }

    public static function setOtp(int $id, string $otp): void
    {
        self::execute('UPDATE ordenes_preventivas SET codigo_otp_validacion = ? WHERE id = ?', [$otp, $id]);
    }

    public static function calendarEvents(): array
    {
        return self::raw(
            'SELECT op.*, e.nombre AS equipo_nombre, c.nombre AS categoria_nombre
             FROM ordenes_preventivas op
             LEFT JOIN equipos e ON e.id = op.equipo_id
             LEFT JOIN categorias_equipo c ON c.id = e.categoria_id
             ORDER BY op.fecha_planificada'
        );
    }
}
