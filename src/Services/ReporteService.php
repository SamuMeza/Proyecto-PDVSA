<?php
namespace App\Services;

use App\Models\OrdenPreventiva;
use App\Models\OrdenCorrectiva;
use App\Models\Equipo;
use App\Models\User;

class ReporteService
{
    public static function dashboardStats(): array
    {
        $preventivasTotal = OrdenPreventiva::count();
        $correctivasTotal = OrdenCorrectiva::count();

        $preventivasCompletadas = (int) OrdenPreventiva::rawOne(
            "SELECT COUNT(*) AS cnt FROM ordenes_preventivas WHERE estado = 'cerrada'"
        )['cnt'] ?? 0;

        $preventivasMes = (int) OrdenPreventiva::rawOne(
            "SELECT COUNT(*) AS cnt FROM ordenes_preventivas
             WHERE estado = 'cerrada' AND MONTH(fecha_cierre_ejecucion) = MONTH(NOW()) AND YEAR(fecha_cierre_ejecucion) = YEAR(NOW())"
        )['cnt'] ?? 0;

        $correctivasAbiertas = (int) OrdenCorrectiva::rawOne(
            "SELECT COUNT(*) AS cnt FROM ordenes_correctivas WHERE estado IN ('reportada', 'en_progreso')"
        )['cnt'] ?? 0;

        $equiposTotal = Equipo::count();
        $usuariosActivos = User::count("estado = 'activo'");

        return [
            'preventivas_total' => $preventivasTotal,
            'preventivas_completadas' => $preventivasCompletadas,
            'preventivas_mes' => $preventivasMes,
            'correctivas_total' => $correctivasTotal,
            'correctivas_abiertas' => $correctivasAbiertas,
            'equipos_total' => $equiposTotal,
            'usuarios_activos' => $usuariosActivos,
            'cumplimiento_pct' => $preventivasTotal > 0
                ? round(($preventivasCompletadas / $preventivasTotal) * 100, 1)
                : 0,
        ];
    }

    public static function cumplimientoStats(): array
    {
        $porMes = OrdenPreventiva::raw(
            "SELECT
                DATE_FORMAT(fecha_planificada, '%Y-%m') AS mes,
                COUNT(*) AS total,
                SUM(CASE WHEN estado = 'cerrada' THEN 1 ELSE 0 END) AS completadas,
                SUM(CASE WHEN estado = 'suspendida' THEN 1 ELSE 0 END) AS suspendidas,
                SUM(CASE WHEN estado IN ('planificada', 'en_curso') THEN 1 ELSE 0 END) AS pendientes
             FROM ordenes_preventivas
             WHERE fecha_planificada >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY mes
             ORDER BY mes"
        );

        $correctivasResumen = OrdenCorrectiva::rawOne(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN estado = 'cerrada' THEN 1 ELSE 0 END) AS cerradas,
                SUM(CASE WHEN estado IN ('reportada', 'en_progreso') THEN 1 ELSE 0 END) AS abiertas,
                SUM(CASE WHEN estado = 'cancelada' THEN 1 ELSE 0 END) AS canceladas
             FROM ordenes_correctivas"
        );

        return [
            'por_mes' => $porMes,
            'correctivas' => $correctivasResumen,
        ];
    }

    public static function fallasStats(): array
    {
        $porTipo = OrdenCorrectiva::raw(
            "SELECT
                tf.nombre AS tipo_falla,
                COUNT(*) AS cantidad
             FROM ordenes_correctivas oc
             LEFT JOIN tipos_falla tf ON tf.id = oc.tipo_falla_id
             GROUP BY tf.nombre
             ORDER BY cantidad DESC"
        );

        $porZona = OrdenCorrectiva::raw(
            "SELECT
                z.nombre AS zona,
                COUNT(*) AS cantidad
             FROM ordenes_correctivas oc
             INNER JOIN equipos e ON e.id = oc.equipo_id
             LEFT JOIN zonas z ON z.id = e.zona_id
             GROUP BY z.nombre
             ORDER BY cantidad DESC"
        );

        $porMes = OrdenCorrectiva::raw(
            "SELECT
                DATE_FORMAT(creada_en, '%Y-%m') AS mes,
                COUNT(*) AS cantidad
             FROM ordenes_correctivas
             WHERE creada_en >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY mes
             ORDER BY mes"
        );

        return [
            'por_tipo' => $porTipo,
            'por_zona' => $porZona,
            'por_mes' => $porMes,
        ];
    }

    public static function resumenMensual(): array
    {
        return OrdenPreventiva::raw(
            "SELECT
                DATE_FORMAT(fecha_planificada, '%Y-%m') AS mes,
                COUNT(*) AS preventivas_total,
                SUM(CASE WHEN estado = 'cerrada' THEN 1 ELSE 0 END) AS preventivas_completadas,
                SUM(CASE WHEN estado = 'en_curso' THEN 1 ELSE 0 END) AS preventivas_en_curso,
                SUM(CASE WHEN estado = 'suspendida' THEN 1 ELSE 0 END) AS preventivas_suspendidas
             FROM ordenes_preventivas
             WHERE fecha_planificada >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY mes
             ORDER BY mes"
        );
    }

    public static function tecnicosStats(): array
    {
        return OrdenCorrectiva::raw(
            "SELECT
                u.nombre_completo AS tecnico,
                COUNT(*) AS total_asignadas,
                SUM(CASE WHEN oc.estado = 'cerrada' THEN 1 ELSE 0 END) AS completadas,
                SUM(CASE WHEN oc.estado IN ('reportada', 'en_progreso') THEN 1 ELSE 0 END) AS pendientes,
                ROUND(
                    CASE WHEN COUNT(*) > 0
                        THEN (SUM(CASE WHEN oc.estado = 'cerrada' THEN 1 ELSE 0 END) / COUNT(*)) * 100
                        ELSE 0
                    END, 1
                ) AS cumplimiento_pct
             FROM ordenes_correctivas oc
             INNER JOIN usuarios u ON u.id = oc.mantenedor_id
             GROUP BY u.id, u.nombre_completo
             ORDER BY total_asignadas DESC"
        );
    }
}
