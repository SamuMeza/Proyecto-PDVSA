                <div class="page-header">
                    <h1 class="page-title">Usuarios</h1>
                </div>
                <div class="page-card">
                    <p>Módulo de usuarios — contenido en desarrollo.</p>
                    <?php if (\App\Services\AuthService::isAdmin()): ?>
                        <p><a href="<?= \App\Core\App::BASE_PATH ?>/auth/register.php" class="btn btn-primary">Registrar nuevo usuario</a></p>
                    <?php endif; ?>
                </div>

