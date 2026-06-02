<?php
namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Services\AuthService;
use App\Services\ReporteService;
use App\Services\ChartGenerator;

class ReporteController
{
    public function index(): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('reportes', 'ver');

        $stats = ReporteService::dashboardStats();

        Response::view('reportes/index', [
            'stats' => $stats,
            'pageTitle' => 'Reportes',
            'pageSlug' => 'reportes',
        ]);
    }

    public function cumplimiento(): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('reportes', 'ver');

        $data = ReporteService::cumplimientoStats();
        $stats = $data['correctivas'];

        $labels = array_column($data['por_mes'], 'mes');
        $completadas = array_column($data['por_mes'], 'completadas');
        $chartData = ChartGenerator::barChartData($labels, $completadas, 'Completadas');

        Response::view('reportes/cumplimiento', [
            'data' => $data,
            'stats' => $stats,
            'chartData' => $chartData,
            'pageTitle' => 'Cumplimiento',
            'pageSlug' => 'reportes',
        ]);
    }

    public function fallas(): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('reportes', 'ver');

        $fallas = ReporteService::fallasStats();

        $tipoLabels = array_column($fallas['por_tipo'], 'tipo_falla');
        $tipoValues = array_column($fallas['por_tipo'], 'cantidad');
        $chartPorTipo = ChartGenerator::pieChartData($tipoLabels, $tipoValues);

        $zonaLabels = array_column($fallas['por_zona'], 'zona');
        $zonaValues = array_column($fallas['por_zona'], 'cantidad');
        $chartPorZona = ChartGenerator::barChartData($zonaLabels, $zonaValues, 'Fallas');

        Response::view('reportes/fallas', [
            'fallas' => $fallas,
            'chartPorTipo' => $chartPorTipo,
            'chartPorZona' => $chartPorZona,
            'pageTitle' => 'Estadísticas de Fallas',
            'pageSlug' => 'reportes',
        ]);
    }

    public function resumenMensual(): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('reportes', 'ver');

        $resumen = ReporteService::resumenMensual();

        Response::view('reportes/resumen-mensual', [
            'resumen' => $resumen,
            'pageTitle' => 'Resumen Mensual',
            'pageSlug' => 'reportes',
        ]);
    }

    public function tecnicos(): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('reportes', 'ver');

        $tecnicos = ReporteService::tecnicosStats();

        Response::view('reportes/tecnicos', [
            'tecnicos' => $tecnicos,
            'pageTitle' => 'Rendimiento por Técnico',
            'pageSlug' => 'reportes',
        ]);
    }
}
