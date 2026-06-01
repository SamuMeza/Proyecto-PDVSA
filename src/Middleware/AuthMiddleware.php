<?php
namespace App\Middleware;

use App\Core\App;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;

class AuthMiddleware
{
    public static function authenticated(): void
    {
        Session::start();
        if (!AuthService::check()) {
            Response::redirect(App::BASE_PATH . '/login');
        }
    }

    public static function admin(): void
    {
        self::authenticated();
        if (!AuthService::isAdmin()) {
            Response::redirect(App::BASE_PATH . '/?error=sin_permiso');
        }
    }

    public static function permission(string $module, string $action): callable
    {
        return function () use ($module, $action) {
            self::authenticated();
            if (!AuthService::hasPermission($module, $action)) {
                Response::redirect(App::BASE_PATH . '/?error=sin_permiso');
            }
        };
    }
}
