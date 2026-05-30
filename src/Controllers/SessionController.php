<?php
namespace App\Controllers;

use App\Core\App;
use App\Core\Session;
use App\Services\AuthService;
use App\Models\User;

class SessionController
{
    public function keepalive(): void
    {
        Session::start();
        if (!AuthService::check()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false]);
            exit;
        }

        $userId = (int) Session::get('usuario_id');
        $token = Session::get('sesion_token');
        $renewed = AuthService::renewSession($userId, $token);

        if (!$renewed) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false]);
            exit;
        }

        $user = User::find($userId);
        $expiresAt = $user['sesion_expira_en'] ?? '';

        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'expires_at' => $expiresAt]);
        exit;
    }
}
