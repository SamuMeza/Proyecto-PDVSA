<?php
namespace App\Models;

class Checklist extends Model
{
    protected static function table(): string { return 'checklists'; }
    protected static function primaryKey(): string { return 'id'; }
}
