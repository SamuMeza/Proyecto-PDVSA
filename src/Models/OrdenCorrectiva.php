<?php
namespace App\Models;

class OrdenCorrectiva extends Model
{
    protected static function table(): string { return 'ordenes_correctivas'; }
    protected static function primaryKey(): string { return 'id'; }

    public static function validStates(): array
    {
        return ['reportada', 'en_progreso', 'completada', 'cerrada', 'cancelada'];
    }

    public static function allowedTransitions(string $currentState): array
    {
        return match ($currentState) {
            'reportada' => ['en_progreso', 'cancelada'],
            'en_progreso' => ['completada', 'cancelada'],
            'completada' => ['cerrada', 'en_progreso'],
            'cerrada' => [],
            'cancelada' => [],
            default => [],
        };
    }
}
