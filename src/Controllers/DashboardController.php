<?php
namespace App\Controllers;

use App\Core\App;
use App\Core\Session;
use App\Services\AuthService;

class DashboardController
{
    public function index(): void
    {
        AuthService::requireAuth();

        $error = $_GET['error'] ?? '';
        $pageTitle = 'Inicio';
        $pageSlug = 'index';

        require dirname(__DIR__, 2) . '/public/includes/layout.php';
        require dirname(__DIR__, 2) . '/src/Views/dashboard/index.php';
        require dirname(__DIR__, 2) . '/public/includes/layout_footer.php';
    }
}
