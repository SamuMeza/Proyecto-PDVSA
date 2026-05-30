<?php
/**
 * Front controller — Sistema PDVSA
 *
 * Punto de entrada único. Requiere composer autoload.
 * Si no hay autoload, redirige al layout legacy.
 */

$autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;

    $router = new App\Core\Router();
    $routes = require __DIR__ . '/config/routes.php';
    $routes($router);

    $app = App\Core\App::getInstance();
    $app->setRouter($router);
    $app->run();
} else {
    require_once __DIR__ . '/config/database.php';
    header('Location: ' . (defined('BASE_PATH') ? BASE_PATH : '/sistema_pdvsa') . '/public/index.php');
    exit;
}
