<?php
require_once __DIR__ . '/../config/autoload.php';

use App\Core\App;
use App\Services\AuthService;

AuthService::requirePermission('reportes', 'ver');

$pageTitle = 'Reportes';
$pageSlug = 'reportes';

require __DIR__ . '/includes/layout.php';
require __DIR__ . '/../src/Views/reportes/index.php';
require __DIR__ . '/includes/layout_footer.php';
