<?php
$pageTitle = 'Inicio';
$pageSlug  = 'index';
require __DIR__ . '/includes/layout.php';
?>
                <h1 class="page-title">Inicio</h1>
                <?php if (isset($_GET['error']) && $_GET['error'] === 'sin_permiso'): ?>
                    <div class="alert alert-error">No tiene permisos para acceder a esa sección. Solo el Administrador puede registrar usuarios.</div>
                <?php endif; ?>
                <div class="page-card">
                    <p>Bienvenido al sistema de gestión PDVSA.</p>
                </div>
<?php require __DIR__ . '/includes/layout_footer.php'; ?>
