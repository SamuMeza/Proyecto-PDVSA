<?php
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
    $router = new App\Core\Router();
    $routes = require __DIR__ . '/../config/routes.php';
    $routes($router);
    $app = App\Core\App::getInstance();
    $app->setRouter($router);
    $app->run();
} else {
    require_once __DIR__ . '/../config/autoload.php';
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../src/Core/App.php';
    header('Location: ' . App\Core\App::BASE_PATH . '/');
    exit;
}
