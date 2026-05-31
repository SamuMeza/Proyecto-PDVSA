<?php
namespace App\Models;

class EjecucionChecklistItem extends Model
{
    protected static function table(): string { return 'ejecucion_checklist_items'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function findByCorrectiva(int $correctivaId): array
    {
        return self::findWhere('orden_correctiva_id = ?', [$correctivaId], 'id');
    }

    public static function upsert(int $correctivaId, int $itemId, string $valor): void
    {
        $existing = self::findFirstWhere('orden_correctiva_id = ? AND checklist_item_id = ?', [$correctivaId, $itemId]);
        if ($existing) {
            self::execute(
                'UPDATE ejecucion_checklist_items SET valor = ?, actualizado_en = NOW() WHERE id = ?',
                [$valor, $existing['id']]
            );
        } else {
            self::insert([
                'orden_correctiva_id' => $correctivaId,
                'checklist_item_id' => $itemId,
                'valor' => $valor,
                'creado_en' => date('Y-m-d H:i:s'),
                'actualizado_en' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
