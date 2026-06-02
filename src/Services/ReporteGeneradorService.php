<?php
namespace App\Services;

use App\Core\Database;
use App\Core\Session;
use App\Models\ReporteGenerado;

class ReporteGeneradorService
{
    /**
     * Cuenta registros que coinciden con los filtros para un tipo de reporte dado.
     */
    public static function contarRegistros(string $tipoReporte, array $filtros): int
    {
        $sql = self::construirQueryCount($tipoReporte, $filtros);
        $pdo = Database::connection();
        $stmt = $pdo->prepare($sql['sql']);
        $stmt->execute($sql['params']);
        return (int) $stmt->fetch()['cnt'];
    }

    /**
     * Obtiene registros para el reporte.
     */
    public static function obtenerRegistros(string $tipoReporte, array $filtros, int $limite = 5000): array
    {
        $sql = self::construirQuery($tipoReporte, $filtros);
        $sql['sql'] .= " LIMIT $limite";
        
        $pdo = Database::connection();
        $stmt = $pdo->prepare($sql['sql']);
        $stmt->execute($sql['params']);
        return $stmt->fetchAll();
    }

    /**
     * Genera un reporte completo (PDF + CSV) y guarda el registro en BD.
     */
    public static function generarReporte(string $tipoReporte, array $filtros, int $usuarioId): array
    {
        $inicio = microtime(true);

        // 1. Limpiar reportes antiguos
        self::limpiarReportesAntiguos();

        // 2. Obtener registros
        $datos = self::obtenerRegistros($tipoReporte, $filtros);
        
        if (empty($datos)) {
            return ['ok' => false, 'error' => 'No se encontraron resultados con los filtros seleccionados'];
        }

        // 3. Crear registro en BD con estado 'generando'
        $registros = self::contarRegistros($tipoReporte, $filtros);
        $reporteId = ReporteGenerado::insert([
            'tipo_reporte' => $tipoReporte,
            'formato' => 'pdf',
            'generado_por_usuario_id' => $usuarioId,
            'parametros_filtros_json' => json_encode($filtros),
            'ruta_archivo' => '',
            'nombre_archivo_descarga' => '',
            'estado' => 'generando',
        ]);

        try {
            // 4. Generar PDF
            $pdfResult = PdfGeneratorService::generarPdf($tipoReporte, $filtros, $datos);
            
            // 5. Generar CSV (complementario)
            $csvResult = CsvGeneratorService::generarCsv($tipoReporte, $filtros, $datos);
            
            $duracion = (int)((microtime(true) - $inicio) * 1000);

            // 6. Actualizar registro con éxito
            ReporteGenerado::update($reporteId, [
                'ruta_archivo' => $pdfResult['ruta'],
                'nombre_archivo_descarga' => $pdfResult['nombre'],
                'ruta_archivo_csv' => $csvResult['ruta'],
                'nombre_archivo_csv' => $csvResult['nombre'],
                'estado' => 'completado',
                'tamano_bytes' => $pdfResult['tamano'],
                'duracion_ms' => $duracion,
            ]);

            return [
                'ok' => true,
                'reporte_id' => $reporteId,
                'pdf' => $pdfResult,
                'csv' => $csvResult,
                'registros' => $registros,
            ];

        } catch (\Exception $e) {
            // 7. Manejar error
            error_log(date('Y-m-d H:i:s') . ' [ReporteGeneradorService::generarReporte] Error: ' . $e->getMessage() . PHP_EOL, 3, dirname(__DIR__, 2) . '/logs/errors.log');
            
            ReporteGenerado::update($reporteId, [
                'estado' => 'error',
                'mensaje_error' => $e->getMessage(),
            ]);

            return ['ok' => false, 'error' => 'Error al generar el reporte. Intente de nuevo.'];
        }
    }

    /**
     * Obtiene el historial de reportes generados.
     */
    public static function obtenerHistorial(int $usuarioId, array $filtros, int $pagina = 1, int $porPagina = 20): array
    {
        $pdo = Database::connection();
        $where = ['1 = 1'];
        $params = [];

        // Si no es admin/supervisor, solo ver sus propios reportes
        if (!self::esAdminOSupervisor()) {
            $where[] = 'generado_por_usuario_id = ?';
            $params[] = $usuarioId;
        }

        if (!empty($filtros['tipo_reporte'])) {
            $where[] = 'tipo_reporte = ?';
            $params[] = $filtros['tipo_reporte'];
        }

        if (!empty($filtros['estado'])) {
            $where[] = 'estado = ?';
            $params[] = $filtros['estado'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $where[] = 'creado_en >= ?';
            $params[] = $filtros['fecha_desde'] . ' 00:00:00';
        }

        if (!empty($filtros['fecha_hasta'])) {
            $where[] = 'creado_en <= ?';
            $params[] = $filtros['fecha_hasta'] . ' 23:59:59';
        }

        $whereStr = implode(' AND ', $where);

        // Contar total
        $stmtCount = $pdo->prepare("SELECT COUNT(*) AS cnt FROM reportes_generados WHERE {$whereStr}");
        $stmtCount->execute($params);
        $total = (int) $stmtCount->fetch()['cnt'];
        $totalPaginas = ceil($total / $porPagina);

        // Obtener registros
        $offset = ($pagina - 1) * $porPagina;
        $paramsPagina = array_merge($params, [$porPagina, $offset]);
        
        $stmt = $pdo->prepare("
            SELECT * FROM reportes_generados 
            WHERE {$whereStr} 
            ORDER BY creado_en DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute($paramsPagina);
        $reportes = $stmt->fetchAll();

        return [
            'reportes' => $reportes,
            'total' => $total,
            'total_paginas' => $totalPaginas,
            'pagina_actual' => $pagina,
        ];
    }

    /**
     * Elimina un reporte (registro BD + archivo físico).
     */
    public static function eliminarReporte(int $reporteId, int $usuarioId): bool
    {
        $reporte = ReporteGenerado::find($reporteId);
        if (!$reporte) {
            return false;
        }

        // Verificar permisos (admin/supervisor pueden eliminar todos)
        if (!self::esAdminOSupervisor() && $reporte['generado_por_usuario_id'] != $usuarioId) {
            return false;
        }

        // Eliminar archivo físico (PDF)
        if (!empty($reporte['ruta_archivo']) && file_exists($reporte['ruta_archivo'])) {
            unlink($reporte['ruta_archivo']);
        }

        // Eliminar archivo físico (CSV)
        if (!empty($reporte['ruta_archivo_csv']) && file_exists($reporte['ruta_archivo_csv'])) {
            unlink($reporte['ruta_archivo_csv']);
        }

        // Eliminar registro de BD
        ReporteGenerado::delete($reporteId);
        return true;
    }

    /**
     * Elimina reportes con más de 90 días.
     */
    public static function limpiarReportesAntiguos(): int
    {
        $pdo = Database::connection();
        
        // Obtener reportes viejos
        $stmt = $pdo->prepare("
            SELECT id, ruta_archivo, ruta_archivo_csv FROM reportes_generados 
            WHERE creado_en < DATE_SUB(NOW(), INTERVAL 90 DAY)
        ");
        $stmt->execute();
        $viejos = $stmt->fetchAll();

        $eliminados = 0;
        foreach ($viejos as $reporte) {
            // Eliminar archivo PDF
            if (!empty($reporte['ruta_archivo']) && file_exists($reporte['ruta_archivo'])) {
                unlink($reporte['ruta_archivo']);
            }
            // Eliminar archivo CSV
            if (!empty($reporte['ruta_archivo_csv']) && file_exists($reporte['ruta_archivo_csv'])) {
                unlink($reporte['ruta_archivo_csv']);
            }
            // Eliminar registro
            ReporteGenerado::delete((int) $reporte['id']);
            $eliminados++;
        }

        return $eliminados;
    }

    /**
     * Construye la query SQL según el tipo de reporte y filtros.
     */
    private static function construirQuery(string $tipoReporte, array $filtros): array
    {
        $sql = '';
        $params = [];

        switch ($tipoReporte) {
            case 'fallas':
                $sql = "SELECT 
                    oc.codigo_unico AS codigo,
                    e.numero_activo_fijo AS equipo,
                    tf.nombre AS tipo_falla,
                    pf.nombre AS prioridad,
                    z.nombre AS zona,
                    u.nombre_completo AS tecnico,
                    oc.fecha_reporte,
                    oc.estado
                FROM ordenes_correctivas oc
                INNER JOIN equipos e ON e.id = oc.equipo_id
                LEFT JOIN tipos_falla tf ON tf.id = oc.tipo_falla_id
                LEFT JOIN prioridades_falla pf ON pf.id = oc.prioridad_id
                LEFT JOIN zonas z ON z.id = oc.zona_id
                LEFT JOIN usuarios u ON u.id = oc.mantenedor_id
                WHERE 1=1";
                
                if (!empty($filtros['tipo_falla_id'])) {
                    $sql .= " AND oc.tipo_falla_id = ?";
                    $params[] = $filtros['tipo_falla_id'];
                }
                if (!empty($filtros['zona_id'])) {
                    $sql .= " AND oc.zona_id = ?";
                    $params[] = $filtros['zona_id'];
                }
                if (!empty($filtros['prioridad_id'])) {
                    $sql .= " AND oc.prioridad_id = ?";
                    $params[] = $filtros['prioridad_id'];
                }
                if (!empty($filtros['estado'])) {
                    $sql .= " AND oc.estado = ?";
                    $params[] = $filtros['estado'];
                }
                if (!empty($filtros['mantenedor_id'])) {
                    $sql .= " AND oc.mantenedor_id = ?";
                    $params[] = $filtros['mantenedor_id'];
                }
                if (!empty($filtros['fecha_desde'])) {
                    $sql .= " AND oc.fecha_reporte >= ?";
                    $params[] = $filtros['fecha_desde'];
                }
                if (!empty($filtros['fecha_hasta'])) {
                    $sql .= " AND oc.fecha_reporte <= ?";
                    $params[] = $filtros['fecha_hasta'];
                }
                $sql .= " ORDER BY oc.fecha_reporte DESC";
                break;

            case 'cumplimiento':
                $sql = "SELECT 
                    op.codigo_unico AS codigo,
                    e.numero_activo_fijo AS equipo,
                    nm.nombre_nivel AS nivel_mantenimiento,
                    z.nombre AS zona,
                    op.fecha_planificada,
                    op.fecha_cierre_ejecucion,
                    op.estado
                FROM ordenes_preventivas op
                INNER JOIN equipos e ON e.id = op.equipo_id
                LEFT JOIN niveles_mantenimiento nm ON nm.id = op.nivel_mantenimiento_id
                LEFT JOIN zonas z ON z.id = e.zona_id
                WHERE 1=1";
                
                if (!empty($filtros['estado'])) {
                    $sql .= " AND op.estado = ?";
                    $params[] = $filtros['estado'];
                }
                if (!empty($filtros['zona_id'])) {
                    $sql .= " AND e.zona_id = ?";
                    $params[] = $filtros['zona_id'];
                }
                if (!empty($filtros['nivel_mantenimiento_id'])) {
                    $sql .= " AND op.nivel_mantenimiento_id = ?";
                    $params[] = $filtros['nivel_mantenimiento_id'];
                }
                if (!empty($filtros['fecha_desde'])) {
                    $sql .= " AND op.fecha_planificada >= ?";
                    $params[] = $filtros['fecha_desde'];
                }
                if (!empty($filtros['fecha_hasta'])) {
                    $sql .= " AND op.fecha_planificada <= ?";
                    $params[] = $filtros['fecha_hasta'];
                }
                $sql .= " ORDER BY op.fecha_planificada DESC";
                break;

            case 'resumen-mensual':
                $sql = "SELECT 
                    DATE_FORMAT(op.fecha_planificada, '%Y-%m') AS mes,
                    COUNT(*) AS total,
                    SUM(CASE WHEN op.estado = 'cerrada' THEN 1 ELSE 0 END) AS completadas,
                    SUM(CASE WHEN op.estado = 'en_curso' THEN 1 ELSE 0 END) AS en_curso,
                    SUM(CASE WHEN op.estado = 'suspendida' THEN 1 ELSE 0 END) AS suspendidas,
                    ROUND(
                        CASE WHEN COUNT(*) > 0 
                            THEN (SUM(CASE WHEN op.estado = 'cerrada' THEN 1 ELSE 0 END) / COUNT(*)) * 100 
                            ELSE 0 
                        END, 1
                    ) AS cumplimiento_pct
                FROM ordenes_preventivas op
                INNER JOIN equipos e ON e.id = op.equipo_id
                LEFT JOIN zonas z ON z.id = e.zona_id
                LEFT JOIN niveles_mantenimiento nm ON nm.id = op.nivel_mantenimiento_id
                WHERE 1=1";
                
                if (!empty($filtros['mes_desde'])) {
                    $sql .= " AND DATE_FORMAT(op.fecha_planificada, '%Y-%m') >= ?";
                    $params[] = $filtros['mes_desde'];
                }
                if (!empty($filtros['mes_hasta'])) {
                    $sql .= " AND DATE_FORMAT(op.fecha_planificada, '%Y-%m') <= ?";
                    $params[] = $filtros['mes_hasta'];
                }
                if (!empty($filtros['zona_id'])) {
                    $sql .= " AND e.zona_id = ?";
                    $params[] = $filtros['zona_id'];
                }
                if (!empty($filtros['nivel_mantenimiento_id'])) {
                    $sql .= " AND op.nivel_mantenimiento_id = ?";
                    $params[] = $filtros['nivel_mantenimiento_id'];
                }
                $sql .= " GROUP BY mes ORDER BY mes DESC";
                break;

            case 'tecnicos':
                $sql = "SELECT 
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
                LEFT JOIN zonas z ON z.id = oc.zona_id
                WHERE 1=1";
                
                if (!empty($filtros['mantenedor_id'])) {
                    $sql .= " AND oc.mantenedor_id = ?";
                    $params[] = $filtros['mantenedor_id'];
                }
                if (!empty($filtros['estado'])) {
                    $sql .= " AND oc.estado = ?";
                    $params[] = $filtros['estado'];
                }
                if (!empty($filtros['zona_id'])) {
                    $sql .= " AND oc.zona_id = ?";
                    $params[] = $filtros['zona_id'];
                }
                if (!empty($filtros['fecha_desde'])) {
                    $sql .= " AND oc.fecha_reporte >= ?";
                    $params[] = $filtros['fecha_desde'];
                }
                if (!empty($filtros['fecha_hasta'])) {
                    $sql .= " AND oc.fecha_reporte <= ?";
                    $params[] = $filtros['fecha_hasta'];
                }
                $sql .= " GROUP BY u.id, u.nombre_completo ORDER BY total_asignadas DESC";
                break;

            default:
                throw new \InvalidArgumentException("Tipo de reporte no válido: {$tipoReporte}");
        }

        return ['sql' => $sql, 'params' => $params];
    }

    /**
     * Construye query de conteo.
     */
    private static function construirQueryCount(string $tipoReporte, array $filtros): array
    {
        $result = self::construirQuery($tipoReporte, $filtros);
        
        // Contar envolviendo en subquery (funciona para todos los tipos)
        return [
            'sql' => "SELECT COUNT(*) AS cnt FROM (" . $result['sql'] . ") AS sub",
            'params' => $result['params']
        ];
    }

    /**
     * Verifica si el usuario actual es admin o supervisor.
     */
    private static function esAdminOSupervisor(): bool
    {
        $rol = Session::get('rol_nombre', '');
        return in_array($rol, ['Administrador', 'Supervisor']);
    }
}
