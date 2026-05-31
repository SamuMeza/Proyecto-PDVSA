<?php
namespace App\Middleware;

use App\Core\Session;

class CsrfMiddleware
{
    public static function generateToken(): string
    {
        Session::start();
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
        return $token;
    }

    public static function validateToken(?string $token): bool
    {
        Session::start();
        $stored = Session::get('csrf_token');
        if (!$stored || !$token) return false;
        return hash_equals($stored, $token);
    }

    public static function validateRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!self::validateToken($token)) {
            http_response_code(419);
            exit('CSRF token inválido');
        }
    }

    public static function field(): string
    {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}
