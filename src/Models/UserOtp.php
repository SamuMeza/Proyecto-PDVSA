<?php
namespace App\Models;

class UserOtp extends Model
{
    protected static function table(): string { return 'usuario_otp'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function findByUser(int $userId): ?array
    {
        return self::findFirstWhere('usuario_id = ?', [$userId]);
    }

    public static function clear(int $userId): void
    {
        self::execute('DELETE FROM usuario_otp WHERE usuario_id = ?', [$userId]);
    }

    public static function incrementFailedAttempts(int $userId): void
    {
        self::execute('UPDATE usuario_otp SET intentos_fallidos = intentos_fallidos + 1, actualizado_en = NOW() WHERE usuario_id = ?', [$userId]);
    }
}
