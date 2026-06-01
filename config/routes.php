<?php
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\EquipoController;
use App\Controllers\PreventivoController;
use App\Controllers\CorrectivoController;
use App\Controllers\CalendarioController;
use App\Controllers\UsuarioController;
use App\Controllers\ReporteController;
use App\Controllers\SessionController;
use App\Middleware\AuthMiddleware;

return function (Router $router): void {

    $router->get('/', [DashboardController::class, 'index'], [AuthMiddleware::class . '::authenticated']);

    // Auth routes
    $router->match('/login', [AuthController::class, 'login']);
    $router->match('/otp', [AuthController::class, 'otpVerify']);
    $router->get('/logout', [AuthController::class, 'logout']);

    // Admin routes
    $router->match('/register', [AuthController::class, 'register'], [AuthMiddleware::class . '::admin']);

    // Public routes (authenticated)
    $router->get('/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class . '::authenticated']);
    $router->match('/equipos', [EquipoController::class, 'index'], [AuthMiddleware::class . '::authenticated']);
    $router->match('/preventivas', [PreventivoController::class, 'index'], [AuthMiddleware::class . '::authenticated']);
    $router->match('/correctivas', [CorrectivoController::class, 'index'], [AuthMiddleware::class . '::authenticated']);
    $router->match('/calendario', [CalendarioController::class, 'index'], [AuthMiddleware::class . '::authenticated']);
    $router->match('/reportes', [ReporteController::class, 'index'], [AuthMiddleware::class . '::authenticated']);
    $router->get('/reportes/cumplimiento', [ReporteController::class, 'cumplimiento'], [AuthMiddleware::class . '::authenticated']);
    $router->get('/reportes/fallas', [ReporteController::class, 'fallas'], [AuthMiddleware::class . '::authenticated']);
    $router->get('/reportes/resumen-mensual', [ReporteController::class, 'resumenMensual'], [AuthMiddleware::class . '::authenticated']);
    $router->get('/reportes/tecnicos', [ReporteController::class, 'tecnicos'], [AuthMiddleware::class . '::authenticated']);

    // Usuarios CRUD
    $router->get('/usuarios', [UsuarioController::class, 'index'], [AuthMiddleware::class . '::authenticated']);
    $router->match('/usuarios/crear', [UsuarioController::class, 'create'], [AuthMiddleware::class . '::authenticated']);
    $router->match('/usuarios/editar/{id}', [UsuarioController::class, 'edit'], [AuthMiddleware::class . '::authenticated']);
    $router->post('/usuarios/toggle', [UsuarioController::class, 'toggleStatus'], [AuthMiddleware::class . '::authenticated']);
    $router->get('/usuarios/roles', [UsuarioController::class, 'roles'], [AuthMiddleware::class . '::authenticated']);

    // API-like routes
    $router->get('/session/keepalive', [SessionController::class, 'keepalive']);
};
