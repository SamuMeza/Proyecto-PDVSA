                <div class="page-header">
                    <h1 class="page-title">Inicio</h1>
                </div>

                <?php if ($error === 'sin_permiso'): ?>
                    <div class="alert alert-error">No tiene permisos para acceder a esta sección.</div>
                <?php endif; ?>

                <div class="page-card">
                    <h2>Bienvenido, <?= htmlspecialchars($_SESSION['nombre_completo'] ?? '') ?></h2>
                    <p>Sistema de Mantenimiento PDVSA — Punta Mata</p>
                </div>
<?php require dirname(__DIR__, 3) . '/public/includes/layout_footer.php'; ?>
