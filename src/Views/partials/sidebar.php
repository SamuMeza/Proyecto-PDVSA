<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <span class="sidebar-title">Navegación</span>
        <button class="sidebar-close" id="sidebarClose">&times;</button>
    </div>
    <nav class="sidebar-nav">
        <a href="<?= \App\Core\App::BASE_PATH ?>/" class="nav-item">
            <i class="icon-dashboard"></i> Dashboard
        </a>
        <a href="<?= \App\Core\App::BASE_PATH ?>/equipos" class="nav-item">
            <i class="icon-equipos"></i> Equipos
        </a>
        <a href="<?= \App\Core\App::BASE_PATH ?>/preventivas" class="nav-item">
            <i class="icon-preventivo"></i> Preventivo
        </a>
        <a href="<?= \App\Core\App::BASE_PATH ?>/correctivas" class="nav-item">
            <i class="icon-correctivo"></i> Correctivo
        </a>
        <a href="<?= \App\Core\App::BASE_PATH ?>/calendario" class="nav-item">
            <i class="icon-calendario"></i> Calendario
        </a>
        <a href="<?= \App\Core\App::BASE_PATH ?>/reportes" class="nav-item">
            <i class="icon-reportes"></i> Reportes
        </a>
        <?php if (\App\Services\AuthService::isAdmin()): ?>
        <a href="<?= \App\Core\App::BASE_PATH ?>/usuarios" class="nav-item">
            <i class="icon-usuarios"></i> Usuarios
        </a>
        <?php endif; ?>
    </nav>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
