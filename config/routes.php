<?php
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\EquipoController;
use App\Controllers\PreventivaController;
use App\Controllers\CorrectivaController;
use App\Controllers\CalendarioController;
use App\Controllers\UsuarioController;
use App\Controllers\ReporteController;
use App\Controllers\SessionController;
use App\Middleware\AuthMiddleware;

return function (Router $router): void {

    $router->get('/', [DashboardController::class, 'index'], [AuthMiddleware::class . '::authenticated']);

    // Auth routes
    $router->match('/login', [AuthController::class, 'login']);
    $router->match('/login/otp', [AuthController::class, 'otpVerify']);
    $router->get('/logout', [AuthController::class, 'logout']);

    // Admin routes
    $router->match('/register', [AuthController::class, 'register'], [AuthMiddleware::class . '::admin']);

    // Public routes (authenticated)
    $router->get('/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class . '::authenticated']);
    $router->match('/equipos', [EquipoController::class, 'index'], [AuthMiddleware::class . '::authenticated']);
    $router->match('/preventivas', [PreventivaController::class, 'index'], [AuthMiddleware::class . '::authenticated']);
    $router->match('/correctivas', [CorrectivaController::class, 'index'], [AuthMiddleware::class . '::authenticated']);
    $router->match('/calendario', [CalendarioController::class, 'index'], [AuthMiddleware::class . '::authenticated']);
    $router->match('/usuarios', [UsuarioController::class, 'index'], [AuthMiddleware::class . '::authenticated']);
    $router->match('/reportes', [ReporteController::class, 'index'], [AuthMiddleware::class . '::authenticated']);

    // API-like routes
    $router->get('/session/keepalive', [SessionController::class, 'keepalive']);
};
