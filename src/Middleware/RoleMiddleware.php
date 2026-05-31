<?php
namespace App\Middleware;

use App\Core\App;
use App\Services\AuthService;

class RoleMiddleware
{
    public static function require(string ...$roles): callable
    {
        return function () use ($roles) {
            AuthMiddleware::authenticated();
            $userRole = AuthService::roleName();
            if (!in_array($userRole, $roles, true)) {
                header('Location: ' . App::BASE_PATH . '/public/index.php?error=sin_permiso');
                exit;
            }
        };
    }
}
