<?php
namespace App\Middleware;

use App\Core\App;
use App\Core\Session;
use App\Services\AuthService;

class AuthMiddleware
{
    public static function authenticated(): void
    {
        Session::start();
        if (!AuthService::check()) {
            header('Location: ' . App::BASE_PATH . '/auth/login.php');
            exit;
        }
    }

    public static function admin(): void
    {
        self::authenticated();
        if (!AuthService::isAdmin()) {
            header('Location: ' . App::BASE_PATH . '/public/index.php?error=sin_permiso');
            exit;
        }
    }

    public static function permission(string $module, string $action): callable
    {
        return function () use ($module, $action) {
            self::authenticated();
            if (!AuthService::hasPermission($module, $action)) {
                header('Location: ' . App::BASE_PATH . '/public/index.php?error=sin_permiso');
                exit;
            }
        };
    }
}
