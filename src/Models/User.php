<?php
namespace App\Models;

class User extends Model
{
    protected static function table(): string { return 'usuarios'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function findByUsername(string $username): ?array
    {
        return self::findFirstWhere('nombre_usuario = ? AND estado = ?', [$username, 'activo']);
    }

    public static function findWithRole(int $id): ?array
    {
        return self::rawOne(
            'SELECT u.*, r.nombre AS rol_nombre, r.permisos_json
             FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id
             WHERE u.id = ? AND u.estado = ?',
            [$id, 'activo']
        );
    }

    public static function findByUsernameWithRole(string $username): ?array
    {
        return self::rawOne(
            'SELECT u.*, r.nombre AS rol_nombre, r.permisos_json
             FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id
             WHERE u.nombre_usuario = ? AND u.estado = ?',
            [$username, 'activo']
        );
    }

    public static function allPlanners(): array
    {
        return self::raw(
            'SELECT u.id, u.nombre_completo
             FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id
             WHERE u.estado = ?
             ORDER BY u.nombre_completo',
            ['activo']
        );
    }

    public static function registerSession(int $userId, string $token, string $roleName): void
    {
        $expires = date('Y-m-d H:i:s', time() + \App\Core\Session::timeoutForRole($roleName));
        self::execute(
            'UPDATE usuarios SET sesion_activa_token = ?, sesion_expira_en = ?, ultimo_acceso = NOW(), actualizado_en = NOW() WHERE id = ?',
            [$token, $expires, $userId]
        );
    }

    public static function clearSession(int $userId): void
    {
        self::execute(
            'UPDATE usuarios SET sesion_activa_token = NULL, sesion_expira_en = NULL, actualizado_en = NOW() WHERE id = ?',
            [$userId]
        );
    }

    public static function validateSession(int $userId, string $token): bool
    {
        $row = self::rawOne(
            'SELECT id FROM usuarios WHERE id = ? AND sesion_activa_token = ? AND sesion_expira_en > NOW() AND estado = ?',
            [$userId, $token, 'activo']
        );
        return (bool) $row;
    }

    public static function existsByUsername(string $username): bool
    {
        return self::count('nombre_usuario = ?', [$username]) > 0;
    }
}
