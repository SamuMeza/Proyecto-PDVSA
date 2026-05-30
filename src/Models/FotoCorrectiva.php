<?php
namespace App\Models;

class FotoCorrectiva extends Model
{
    protected static function table(): string { return 'fotos_correctivas'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function findByCorrectiva(int $correctivaId): array
    {
        return self::findWhere('orden_correctiva_id = ?', [$correctivaId], 'id');
    }

    public static function countByCorrectiva(int $correctivaId): int
    {
        return self::count('orden_correctiva_id = ?', [$correctivaId]);
    }

    public static function deleteByCorrectiva(int $correctivaId): void
    {
        self::execute('DELETE FROM fotos_correctivas WHERE orden_correctiva_id = ?', [$correctivaId]);
    }
}
