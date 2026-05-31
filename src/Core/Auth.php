<?php
namespace App\Core;

class Auth
{
    public static function check(): bool
    {
        return \App\Services\AuthService::check();
    }

    public static function user(): ?array
    {
        if (!self::check()) return null;
        return \App\Models\User::findWithRole((int) Session::get('usuario_id'));
    }

    public static function id(): ?int
    {
        if (!self::check()) return null;
        return (int) Session::get('usuario_id');
    }

    public static function role(): string
    {
        return Session::get('rol_nombre', '');
    }

    public static function isAdmin(): bool
    {
        return \App\Services\AuthService::isAdmin();
    }
}
