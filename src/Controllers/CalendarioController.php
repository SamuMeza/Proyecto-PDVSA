<?php
namespace App\Controllers;

use App\Core\App;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;
use App\Models\ConfiguracionSistema;
use App\Models\CategoriaEquipo;
use App\Models\OrdenPreventiva;

class CalendarioController
{
    public function index(): void
    {
        AuthService::requireAuth();

        $rolActual = Session::get('rol_nombre', '');
        $puedeCrear = AuthService::isAdmin();
        $puedeEditar = AuthService::isAdmin() || in_array($rolActual, ['Supervisor', 'Planificador/Programador'], true);
        $puedeEliminar = AuthService::isAdmin();

        $viewParam = $_GET['view'] ?? 'week';
        $viewMode = in_array($viewParam, ['week', 'month'], true) ? $viewParam : 'week';
        $familiaFilter = trim($_GET['familia'] ?? '');
        $dayDetail = (int) ($_GET['day'] ?? 0);

        $familias = CategoriaEquipo::raw(
            "SELECT DISTINCT c.nombre, c.color_calendario FROM categorias_equipo c WHERE c.estado = 'activo' ORDER BY c.nombre"
        );

        $coloresConfigJson = ConfiguracionSistema::get('colores_familia_calendario', '{}');
        $coloresConfig = json_decode($coloresConfigJson, true) ?: [];

        $weekStart = null;
        $weekStartDate = null;
        $weekEndDate = null;
        $dias = [];
        $ordenes = [];
        $eventosPorDia = [];

        $month = null;
        $monthDate = null;
        $year = null;
        $mesNum = null;
        $daysInMonth = null;
        $firstDayOfWeek = null;
        $prevMonth = null;
        $nextMonth = null;
        $ordenesPorDia = [];
        $selectedDay = null;
        $selectedDayOrders = [];

        if ($viewMode === 'week') {
            $weekStart = $_GET['week_start'] ?? date('Y-m-d', strtotime('monday this week'));
            $weekStartDate = new \DateTime($weekStart);
            $weekEndDate = (clone $weekStartDate)->modify('+6 days');
            for ($i = 0; $i < 7; $i++) {
                $dias[] = (clone $weekStartDate)->modify("+$i days")->format('Y-m-d');
            }

            $where = ['op.fecha_planificada BETWEEN ? AND ?', "op.estado NOT IN ('cerrada','suspendida')"];
            $params = [$dias[0], $dias[6]];

            if ($familiaFilter !== '') {
                $where[] = 'c.nombre = ?';
                $params[] = $familiaFilter;
            }

            $ordenes = OrdenPreventiva::raw(
                'SELECT op.id, op.codigo_unico, op.fecha_planificada, op.hora_inicio, op.hora_fin,
                        op.estado, e.nombre AS equipo_nombre, c.nombre AS familia, c.color_calendario
                 FROM ordenes_preventivas op
                 JOIN equipos e ON e.id = op.equipo_id
                 JOIN categorias_equipo c ON c.id = e.categoria_id
                 WHERE ' . implode(' AND ', $where) . '
                 ORDER BY op.fecha_planificada ASC, op.hora_inicio ASC',
                $params
            );

            foreach ($ordenes as $o) {
                $diaIdx = array_search($o['fecha_planificada'], $dias, true);
                if ($diaIdx === false) continue;
                $horaInicio = $o['hora_inicio'] ? (int) substr($o['hora_inicio'], 0, 2) : 0;
                $horaFin = $o['hora_fin'] ? (int) substr($o['hora_fin'], 0, 2) : $horaInicio + 1;
                if ($horaFin <= $horaInicio) $horaFin = $horaInicio + 1;
                $color = $coloresConfig[$o['familia']] ?? $o['color_calendario'] ?? '#7BA7D9';
                for ($h = $horaInicio; $h < $horaFin && $h < 24; $h++) {
                    $eventosPorDia[$diaIdx][$h][] = [
                        'id' => $o['id'],
                        'codigo' => $o['codigo_unico'],
                        'equipo' => $o['equipo_nombre'],
                        'estado' => $o['estado'],
                        'color' => $color,
                        'familia' => $o['familia'],
                        'inicio' => $o['hora_inicio'],
                        'fin' => $o['hora_fin'],
                    ];
                }
            }
        }

        if ($viewMode === 'month') {
            $month = $_GET['month'] ?? date('Y-m');
            $monthDate = new \DateTime($month . '-01');
            $year = (int) $monthDate->format('Y');
            $mesNum = (int) $monthDate->format('m');
            $daysInMonth = (int) $monthDate->format('t');
            $firstDayOfWeek = (int) $monthDate->format('N');
            $prevMonth = (clone $monthDate)->modify('-1 month');
            $nextMonth = (clone $monthDate)->modify('+1 month');

            $where = [
                'op.fecha_planificada BETWEEN ? AND ?',
                "op.estado NOT IN ('cerrada','suspendida')",
            ];
            $params = [$month . '-01', $month . '-' . $daysInMonth];

            if ($familiaFilter !== '') {
                $where[] = 'c.nombre = ?';
                $params[] = $familiaFilter;
            }

            $ordenes = OrdenPreventiva::raw(
                'SELECT op.id, op.codigo_unico, op.fecha_planificada, op.hora_inicio, op.hora_fin,
                        op.estado, e.nombre AS equipo_nombre, c.nombre AS familia, c.color_calendario,
                        nm.nombre_nivel AS nivel
                 FROM ordenes_preventivas op
                 JOIN equipos e ON e.id = op.equipo_id
                 JOIN categorias_equipo c ON c.id = e.categoria_id
                 LEFT JOIN niveles_mantenimiento nm ON nm.id = op.nivel_mantenimiento_id
                 WHERE ' . implode(' AND ', $where) . '
                 ORDER BY op.fecha_planificada ASC, op.hora_inicio ASC',
                $params
            );

            foreach ($ordenes as $o) {
                $dia = (int) substr($o['fecha_planificada'], 8, 2);
                $eventosPorDia[$dia][] = $o;
                $ordenesPorDia[$dia] = ($ordenesPorDia[$dia] ?? 0) + 1;
            }

            if ($dayDetail > 0 && $dayDetail <= $daysInMonth && isset($eventosPorDia[$dayDetail])) {
                $selectedDay = $dayDetail;
                $selectedDayOrders = $eventosPorDia[$dayDetail];
            }
        }

        Response::view('calendario/index', [
            'puedeCrear' => $puedeCrear,
            'puedeEditar' => $puedeEditar,
            'puedeEliminar' => $puedeEliminar,
            'viewMode' => $viewMode,
            'familiaFilter' => $familiaFilter,
            'familias' => $familias,
            'coloresConfig' => $coloresConfig,
            'weekStart' => $weekStart,
            'weekStartDate' => $weekStartDate,
            'weekEndDate' => $weekEndDate,
            'dias' => $dias,
            'ordenes' => $ordenes,
            'eventosPorDia' => $eventosPorDia,
            'month' => $month,
            'monthDate' => $monthDate,
            'year' => $year,
            'mesNum' => $mesNum,
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
            'ordenesPorDia' => $ordenesPorDia,
            'selectedDay' => $selectedDay,
            'selectedDayOrders' => $selectedDayOrders,
            'pageTitle' => 'Calendario de mantenimiento',
            'pageSlug' => 'calendario',
        ]);
    }
}
