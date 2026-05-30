<?php
namespace App\Controllers;

use App\Core\App;
use App\Services\AuthService;
use App\Services\CalendarioService;
use App\Models\ConfiguracionSistema;

class CalendarioController
{
    public function index(): void
    {
        AuthService::requireAuth();

        $events = CalendarioService::getEvents();
        $categorias = CalendarioService::getCategories();

        $filterCategoria = (int) ($_GET['filter_categoria'] ?? 0);
        $viewMode = in_array($_GET['view'] ?? 'month', ['week', 'month'], true) ? $_GET['view'] : 'month';

        $pageTitle = 'Calendario';
        $pageSlug = 'calendario';

        require dirname(__DIR__, 2) . '/public/includes/layout.php';
        require dirname(__DIR__, 2) . '/src/Views/calendario/index.php';
        require dirname(__DIR__, 2) . '/public/includes/layout_footer.php';
    }
}
