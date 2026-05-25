<?php
$pageTitle = 'Usuarios';
$pageSlug  = 'usuarios';
require __DIR__ . '/includes/layout.php';
?>
                <h1 class="page-title">Usuarios</h1>
                <div class="page-card">
                    <p>Módulo de usuarios — contenido en desarrollo.</p>
                    <?php if (esAdministrador()): ?>
                        <p style="margin-top: 1rem;">
                            <a href="<?= BASE_PATH ?>/auth/register.php" class="btn btn-primary">Registrar nuevo usuario</a>
                        </p>
                    <?php endif; ?>
                </div>
<?php require __DIR__ . '/includes/layout_footer.php'; ?>
