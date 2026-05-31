<?php
namespace App\Models;

class LogAuditoria extends Model
{
    protected static function table(): string { return 'logs_auditoria'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function findByCorrectiva(int $correctivaId): array
    {
        return self::findWhere('orden_correctiva_id = ?', [$correctivaId], 'creado_en DESC');
    }
}
