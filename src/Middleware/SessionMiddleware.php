<?php
namespace App\Middleware;

use App\Core\App;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;

class SessionMiddleware
{
    public static function checkInactivity(): void
    {
        Session::start();
        if (!AuthService::check()) return;

        $timeout = Session::timeoutForRole(Session::get('rol_nombre', 'Otros'));
        $lastActivity = Session::get('last_activity', 0);

        if ($lastActivity > 0 && (time() - $lastActivity) > $timeout) {
            AuthService::logout();
            Response::redirect(App::BASE_PATH . '/login?error=sesion_expirada');
        }

        Session::set('last_activity', time());
    }
}
