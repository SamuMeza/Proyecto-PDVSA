<?php
require_once __DIR__ . '/../auth/auth_functions.php';
requerirPermiso('reportes', 'ver');

$pageTitle = 'Reportes';
$pageSlug  = 'reportes';
require __DIR__ . '/includes/layout.php';
?>
                <h1 class="page-title">Reportes</h1>
                <div class="page-card">
                    <p>Módulo de reportes — contenido en desarrollo.</p>
                </div>
<?php require __DIR__ . '/includes/layout_footer.php'; ?>
