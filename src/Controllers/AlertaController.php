<?php
namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Middleware\AuthMiddleware;
use App\Models\Alerta;

class AlertaController
{
    public function index(Request $request): void
    {
        AuthMiddleware::authenticated();
        $userId = (int) Session::get('usuario_id');
        $alertas = Alerta::pendientesByUser($userId);
        Response::json(['alertas' => $alertas, 'total' => count($alertas)]);
    }

    public function count(Request $request): void
    {
        AuthMiddleware::authenticated();
        $userId = (int) Session::get('usuario_id');
        $count = Alerta::countPendientes($userId);
        Response::json(['count' => $count]);
    }

    public function markRead(Request $request): void
    {
        AuthMiddleware::authenticated();
        $id = (int) ($request->body()['id'] ?? 0);
        if ($id > 0) {
            Alerta::marcarLeida($id);
        }
        Response::json(['ok' => true]);
    }

    public function markAllRead(Request $request): void
    {
        AuthMiddleware::authenticated();
        $userId = (int) Session::get('usuario_id');
        Alerta::marcarTodasLeidas($userId);
        Response::json(['ok' => true]);
    }
}
