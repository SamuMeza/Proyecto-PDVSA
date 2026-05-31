<?php
namespace App\Controllers;

use App\Core\App;
use App\Services\AuthService;

class UsuarioController
{
    public function index(): void
    {
        AuthService::requireAuth();
        AuthService::requirePermission('usuarios', 'ver');

        $pageTitle = 'Usuarios';
        $pageSlug = 'usuarios';

        require dirname(__DIR__, 2) . '/public/includes/layout.php';
        require dirname(__DIR__, 2) . '/src/Views/usuarios/index.php';
        require dirname(__DIR__, 2) . '/public/includes/layout_footer.php';
    }
}
