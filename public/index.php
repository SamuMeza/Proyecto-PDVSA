<?php
require_once __DIR__ . '/../config/autoload.php';

use App\Core\App;
use App\Core\Session;
use App\Services\AuthService;

AuthService::requireAuth();

$error = $_GET['error'] ?? '';
$pageTitle = 'Inicio';
$pageSlug = 'index';

require __DIR__ . '/includes/layout.php';
require __DIR__ . '/../src/Views/dashboard/index.php';
require __DIR__ . '/includes/layout_footer.php';
