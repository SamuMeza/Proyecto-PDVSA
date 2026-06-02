<?php
namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\ReporteGenerado;
use App\Services\AuthService;
use App\Services\ReporteGeneradorService;

class ReporteGeneradorController
{
    /**
     * Cuenta registros para un tipo de reporte y filtros (API).
     */
    public function contar(array $params = []): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('reportes', 'ver');

        if (!Request::isPost()) {
            Response::json(['ok' => false, 'error' => 'Método no permitido'], 405);
        }

        $tipoReporte = Request::post('tipo_reporte');
        $filtrosJson = Request::post('filtros', '{}');

        if (empty($tipoReporte)) {
            Response::json(['ok' => false, 'error' => 'Tipo de reporte requerido'], 400);
        }

        $filtros = json_decode($filtrosJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::json(['ok' => false, 'error' => 'Filtros inválidos'], 400);
        }

        try {
            $count = ReporteGeneradorService::contarRegistros($tipoReporte, $filtros);
            Response::json(['ok' => true, 'count' => $count]);
        } catch (\Exception $e) {
            Response::json(['ok' => false, 'error' => 'Error al contar registros'], 500);
        }
    }

    /**
     * Muestra el formulario de generación de reportes.
     */
    public function index(array $params = []): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('reportes', 'ver');

        Response::view('reportes/generar', [
            'pageTitle' => 'Generar Reporte',
            'pageSlug' => 'reportes',
        ]);
    }

    /**
     * Procesa la generación de un reporte (POST).
     */
    public function generar(array $params = []): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('reportes', 'generar');

        if (!Request::isPost()) {
            Response::json(['ok' => false, 'error' => 'Método no permitido'], 405);
        }

        $tipoReporte = Request::post('tipo_reporte');
        $filtrosJson = Request::post('filtros', '{}');

        if (empty($tipoReporte)) {
            Response::json(['ok' => false, 'error' => 'Tipo de reporte requerido'], 400);
        }

        $filtros = json_decode($filtrosJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::json(['ok' => false, 'error' => 'Filtros inválidos'], 400);
        }

        $usuarioId = (int) Session::get('usuario_id');
        
        $result = ReporteGeneradorService::generarReporte($tipoReporte, $filtros, $usuarioId);
        
        Response::json($result, $result['ok'] ? 200 : 400);
    }

    /**
     * Sirve un archivo para descarga (PDF o CSV).
     */
    public function descargar(array $params = []): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('reportes', 'ver');

        $reporteId = $params['id'] ?? null;
        if (!$reporteId) {
            Response::notFound();
        }

        $reporte = ReporteGenerado::find((int) $reporteId);
        if (!$reporte) {
            Response::notFound();
        }

        // Verificar permisos (admin/supervisor ven todos)
        $usuarioId = (int) Session::get('usuario_id');
        $rol = Session::get('rol_nombre', '');
        if (!in_array($rol, ['Administrador', 'Supervisor']) && 
            $reporte['generado_por_usuario_id'] != $usuarioId) {
            Response::forbidden();
        }

        // Verificar que el archivo existe
        if (empty($reporte['ruta_archivo']) || !file_exists($reporte['ruta_archivo'])) {
            Response::notFound();
        }

        // Determinar formato (PDF o CSV)
        $formato = Request::get('formato', 'pdf');
        
        if ($formato === 'csv') {
            // Usar path CSV almacenado en BD
            if (!empty($reporte['ruta_archivo_csv']) && file_exists($reporte['ruta_archivo_csv'])) {
                $filePath = $reporte['ruta_archivo_csv'];
                $contentType = 'text/csv; charset=utf-8';
                $fileName = $reporte['nombre_archivo_csv'] ?? str_replace('.pdf', '.csv', $reporte['nombre_archivo_descarga']);
            } else {
                Response::notFound();
            }
        } else {
            $filePath = $reporte['ruta_archivo'];
            $contentType = 'application/pdf';
            $fileName = $reporte['nombre_archivo_descarga'];
        }

        // Enviar archivo
        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache, must-revalidate');
        
        readfile($filePath);
        exit;
    }

    /**
     * Muestra el historial de reportes generados.
     */
    public function historial(array $params = []): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('reportes', 'ver');

        $usuarioId = (int) Session::get('usuario_id');
        $pagina = (int) Request::get('pagina', 1);
        
        $filtros = [
            'tipo_reporte' => Request::get('tipo_reporte'),
            'estado' => Request::get('estado'),
            'fecha_desde' => Request::get('fecha_desde'),
            'fecha_hasta' => Request::get('fecha_hasta'),
        ];

        $historial = ReporteGeneradorService::obtenerHistorial($usuarioId, $filtros, $pagina);

        Response::view('reportes/historial', [
            'historial' => $historial,
            'filtros' => $filtros,
            'pageTitle' => 'Historial de Reportes',
            'pageSlug' => 'reportes',
        ]);
    }

    /**
     * Elimina un reporte (POST).
     */
    public function eliminar(array $params = []): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('reportes', 'eliminar');

        if (!Request::isPost()) {
            Response::json(['ok' => false, 'error' => 'Método no permitido'], 405);
        }

        $reporteId = $params['id'] ?? null;
        if (!$reporteId) {
            Response::json(['ok' => false, 'error' => 'ID de reporte requerido'], 400);
        }

        $usuarioId = (int) Session::get('usuario_id');
        $result = ReporteGeneradorService::eliminarReporte((int) $reporteId, $usuarioId);
        
        Response::json(['ok' => $result], $result ? 200 : 400);
    }
}
