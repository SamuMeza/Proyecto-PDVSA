<?php
require_once dirname(__DIR__, 2) . '/config/autoload.php';

use App\Core\App;
use App\Core\Session;
use App\Services\AuthService;
use App\Models\ConfiguracionSistema;

Session::start();

$pageTitle = $pageTitle ?? 'Página';
$pageSlug  = $pageSlug ?? '';

$navItems = [
    ['slug' => 'index',       'label' => 'Inicio',           'url' => App::BASE_PATH . '/public/index.php',       'icon' => '⌂'],
    ['slug' => 'equipos',     'label' => 'Equipos',          'url' => App::BASE_PATH . '/public/equipos.php',     'icon' => '⚙'],
    ['slug' => 'preventivas', 'label' => 'Órdenes preventivas', 'url' => App::BASE_PATH . '/public/preventivas.php', 'icon' => '📅'],
    ['slug' => 'calendario', 'label' => 'Calendario', 'url' => App::BASE_PATH . '/public/calendario.php', 'icon' => '🗓'],
    ['slug' => 'correctivas', 'label' => 'Órdenes correctivas', 'url' => App::BASE_PATH . '/public/correctivas.php', 'icon' => '🔧'],
    ['slug' => 'reportes',    'label' => 'Reportes',         'url' => App::BASE_PATH . '/public/reportes.php',    'icon' => '📊'],
    ['slug' => 'usuarios',    'label' => 'Usuarios',         'url' => App::BASE_PATH . '/public/usuarios.php',    'icon' => '👤'],
];

if (AuthService::isAdmin()) {
    $navItems[] = [
        'slug'  => 'registrar',
        'label' => 'Registrar usuario',
        'url'   => App::BASE_PATH . '/auth/register.php',
        'icon'  => '➕',
    ];
}

$logoPath = ConfiguracionSistema::getLogoPath();
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — Sistema PDVSA</title>
    <link rel="stylesheet" href="<?= App::BASE_PATH ?>/public/assets/css/main.css">
</head>
<body>
    <div class="app-layout">
        <aside id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <?php if ($logoPath): ?>
                        <img src="<?= App::BASE_PATH . ltrim($logoPath, '/') ?>" alt="PDVSA" class="sidebar-logo">
                    <?php endif; ?>
                    PDV<span>SA</span>
                </div>
                <button type="button" id="sidebar-toggle" class="sidebar-toggle" aria-label="Ocultar menú">‹</button>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <?php foreach ($navItems as $item): ?>
                        <li>
                            <a href="<?= $item['url'] ?>"
                               class="<?= $pageSlug === $item['slug'] ? 'active' : '' ?>">
                                <span class="nav-icon"><?= $item['icon'] ?></span>
                                <?= htmlspecialchars($item['label']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <strong><?= htmlspecialchars(Session::get('nombre_completo', '')) ?></strong>
                    <?= htmlspecialchars(Session::get('rol_nombre', '')) ?>
                </div>
                <a href="<?= App::BASE_PATH ?>/auth/logout.php" class="btn-logout">Cerrar sesión</a>
            </div>
        </aside>

        <div id="sidebar-overlay" class="sidebar-overlay"></div>

        <div id="main-wrapper" class="main-wrapper">
            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" id="sidebar-open" class="sidebar-open-btn" aria-label="Mostrar menú">☰</button>
                    <span><?= htmlspecialchars($pageTitle) ?></span>
                </div>
                <button type="button" class="theme-toggle" aria-label="Cambiar tema">
                    <span class="theme-icon-light">☀</span>
                    <span class="theme-icon-dark">☽</span>
                </button>
            </header>
            <main class="page-content">
