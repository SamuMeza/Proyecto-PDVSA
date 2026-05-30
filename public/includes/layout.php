<?php
/**
 * Layout compartido con barra lateral y tema.
 * Variables requeridas: $pageTitle, $pageSlug (opcional, para nav activo)
 */
require_once dirname(__DIR__, 2) . '/auth/auth_functions.php';
requerirAutenticacion();

$pageTitle = $pageTitle ?? 'Página';
$pageSlug  = $pageSlug ?? '';

$navItems = [
    ['slug' => 'index',       'label' => 'Inicio',           'url' => BASE_PATH . '/public/index.php',       'icon' => '⌂'],
    ['slug' => 'equipos',     'label' => 'Equipos',          'url' => BASE_PATH . '/public/equipos.php',     'icon' => '⚙'],
    ['slug' => 'preventivas', 'label' => 'Órdenes preventivas', 'url' => BASE_PATH . '/public/preventivas.php', 'icon' => '📅'],
    ['slug' => 'calendario', 'label' => 'Calendario', 'url' => BASE_PATH . '/public/calendario.php', 'icon' => '🗓'],
    ['slug' => 'correctivas', 'label' => 'Órdenes correctivas', 'url' => BASE_PATH . '/public/correctivas.php', 'icon' => '🔧'],
    ['slug' => 'reportes',    'label' => 'Reportes',         'url' => BASE_PATH . '/public/reportes.php',    'icon' => '📊'],
    ['slug' => 'usuarios',    'label' => 'Usuarios',         'url' => BASE_PATH . '/public/usuarios.php',    'icon' => '👤'],
];

if (esAdministrador()) {
    $navItems[] = [
        'slug'  => 'registrar',
        'label' => 'Registrar usuario',
        'url'   => BASE_PATH . '/auth/register.php',
        'icon'  => '➕',
    ];
}

$logoPath = obtenerRutaLogoPdvsa();
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — Sistema PDVSA</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/css/styles.css">
</head>
<body>
    <div class="app-layout">
        <aside id="sidebar" class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <?php if ($logoPath): ?>
                        <img src="<?= htmlspecialchars($logoPath) ?>" alt="PDVSA" class="sidebar-logo">
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
                    <strong><?= htmlspecialchars($_SESSION['nombre_completo'] ?? '') ?></strong>
                    <?= htmlspecialchars($_SESSION['rol_nombre'] ?? '') ?>
                </div>
                <a href="<?= BASE_PATH ?>/auth/logout.php" class="btn-logout">Cerrar sesión</a>
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
