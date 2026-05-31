<?php
namespace App\Core;

class Session
{
    public const TIMEOUT_ADMIN = 600;
    public const TIMEOUT_SUPERVISOR = 1200;
    public const TIMEOUT_OTHER = 2100;
    public const COOKIE_LIFETIME = 28800;

    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => (int) (getenv('SESION_HORAS') ?: 8) * 3600,
                'path' => '/sistema_pdvsa/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public static function isStarted(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function timeoutForRole(string $roleName): int
    {
        return match ($roleName) {
            'Administrador' => (int) (getenv('SESSION_TIMEOUT_ADMIN') ?: self::TIMEOUT_ADMIN),
            'Supervisor' => (int) (getenv('SESSION_TIMEOUT_SUPERVISOR') ?: self::TIMEOUT_SUPERVISOR),
            default => (int) (getenv('SESSION_TIMEOUT_OTHER') ?: self::TIMEOUT_OTHER),
        };
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
