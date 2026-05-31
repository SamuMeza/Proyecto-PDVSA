<?php
namespace App\Controllers;

use App\Core\App;
use App\Core\Session;
use App\Services\AuthService;
use App\Services\CorrectivaService;

class CorrectivaController
{
    public function index(): void
    {
        AuthService::requireAuth();
        if (!CorrectivaService::checkAccess()) {
            header('Location: ' . App::BASE_PATH . '/public/index.php?error=sin_permiso');
            exit;
        }

        $pageTitle = 'Órdenes Correctivas';
        $pageSlug = 'correctivas';

        require dirname(__DIR__, 2) . '/public/includes/layout.php';
        require dirname(__DIR__, 2) . '/src/Views/correctivas/index.php';
        require dirname(__DIR__, 2) . '/public/includes/layout_footer.php';
    }
}
