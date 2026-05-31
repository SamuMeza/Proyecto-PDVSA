<?php
namespace App\Models;

class ChecklistItem extends Model
{
    protected static function table(): string { return 'checklist_items'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function findByChecklist(int $checklistId): array
    {
        return self::findWhere('checklist_id = ?', [$checklistId], 'orden');
    }
}
