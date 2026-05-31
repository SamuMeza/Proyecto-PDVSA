<?php
namespace App\Services;

use App\Models\OrdenPreventiva;
use App\Models\CategoriaEquipo;

class CalendarioService
{
    public static function getEvents(): array
    {
        return OrdenPreventiva::calendarEvents();
    }

    public static function getCategories(): array
    {
        return CategoriaEquipo::allActive();
    }

    public static function getEventsByDate(string $date): array
    {
        $events = self::getEvents();
        return array_filter($events, fn($e) => $e['fecha_planificada'] === $date);
    }
}
