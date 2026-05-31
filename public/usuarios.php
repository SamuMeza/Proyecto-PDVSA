<?php
require_once __DIR__ . '/../config/autoload.php';

use App\Core\App;
use App\Services\AuthService;

AuthService::requirePermission('usuarios', 'ver');

$pageTitle = 'Usuarios';
$pageSlug = 'usuarios';

require __DIR__ . '/includes/layout.php';
require __DIR__ . '/../src/Views/usuarios/index.php';
require __DIR__ . '/includes/layout_footer.php';
