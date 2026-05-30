<?php
namespace App\Controllers;

use App\Core\App;
use App\Services\AuthService;

class ReporteController
{
    public function index(): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('reportes', 'ver');

        $pageTitle = 'Reportes';
        $pageSlug = 'reportes';

        require dirname(__DIR__, 2) . '/public/includes/layout.php';
        require dirname(__DIR__, 2) . '/src/Views/reportes/index.php';
        require dirname(__DIR__, 2) . '/public/includes/layout_footer.php';
    }
}
