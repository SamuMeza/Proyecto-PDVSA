<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <span class="sidebar-title">Navegación</span>
        <button class="sidebar-close" id="sidebarClose">&times;</button>
    </div>
    <nav class="sidebar-nav">
        <a href="<?= \App\Core\App::BASE_PATH ?>/public/index.php" class="nav-item">
            <i class="icon-dashboard"></i> Dashboard
        </a>
        <a href="<?= \App\Core\App::BASE_PATH ?>/public/equipos.php" class="nav-item">
            <i class="icon-equipos"></i> Equipos
        </a>
        <a href="<?= \App\Core\App::BASE_PATH ?>/public/preventivas.php" class="nav-item">
            <i class="icon-preventivo"></i> Preventivo
        </a>
        <a href="<?= \App\Core\App::BASE_PATH ?>/public/correctivas.php" class="nav-item">
            <i class="icon-correctivo"></i> Correctivo
        </a>
        <a href="<?= \App\Core\App::BASE_PATH ?>/public/calendario.php" class="nav-item">
            <i class="icon-calendario"></i> Calendario
        </a>
        <a href="<?= \App\Core\App::BASE_PATH ?>/public/reportes.php" class="nav-item">
            <i class="icon-reportes"></i> Reportes
        </a>
        <?php if (\App\Services\AuthService::isAdmin()): ?>
        <a href="<?= \App\Core\App::BASE_PATH ?>/public/usuarios.php" class="nav-item">
            <i class="icon-usuarios"></i> Usuarios
        </a>
        <?php endif; ?>
    </nav>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
