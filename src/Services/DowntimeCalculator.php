<?php
namespace App\Services;

class DowntimeCalculator
{
    public static function calculate(string $fechaInicio, string $horaInicio, ?string $fechaFin, ?string $horaFin): int
    {
        if (!$fechaFin || !$horaFin) return 0;

        $start = strtotime($fechaInicio . ' ' . $horaInicio);
        $end = strtotime($fechaFin . ' ' . $horaFin);

        if ($end <= $start) return 0;
        return (int) round(($end - $start) / 60);
    }

    public static function formatHours(int $minutes): string
    {
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return "{$h}h {$m}min";
    }

    public static function classify(int $minutes): string
    {
        return match (true) {
            $minutes <= 60 => 'corto',
            $minutes <= 480 => 'medio',
            $minutes <= 1440 => 'largo',
            default => 'critico',
        };
    }
}
