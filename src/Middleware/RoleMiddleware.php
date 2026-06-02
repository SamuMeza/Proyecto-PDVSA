<?php
namespace App\Middleware;

use App\Core\App;
use App\Core\Response;
use App\Services\AuthService;

class RoleMiddleware
{
    public static function require(string ...$roles): callable
    {
        return function () use ($roles) {
            AuthMiddleware::authenticated();
            $userRole = AuthService::roleName();
            if (!in_array($userRole, $roles, true)) {
                Response::redirect(App::BASE_PATH . '/?error=sin_permiso');
            }
        };
    }
}
