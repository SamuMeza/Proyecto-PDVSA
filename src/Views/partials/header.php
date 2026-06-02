<header class="main-header">
    <div class="header-left">
        <button class="sidebar-toggle" id="sidebarToggle">&#9776;</button>
        <img src="<?= \App\Core\App::BASE_PATH ?>/public/assets/images/logo-pdvsa.jpg" alt="PDVSA" class="header-logo" height="32">
        <h1 class="header-title"><?= $title ?? 'Sistema PDVSA' ?></h1>
    </div>
    <div class="header-right">
        <span class="header-user">
            <?= htmlspecialchars(\App\Core\Session::get('nombre_completo', '')) ?>
        </span>
        <a href="<?= \App\Core\App::BASE_PATH ?>/logout" class="btn-logout">Cerrar sesión</a>
    </div>
</header>
