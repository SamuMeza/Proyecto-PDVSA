<?php
namespace App\Services;

use App\Models\NivelMantenimiento;
use App\Models\OrdenPreventiva;

class CalendarGenerator
{
    public static function generateForYear(int $year): array
    {
        $generated = [];
        $niveles = NivelMantenimiento::findWhere("estado = 'activo' AND es_automatico = ?", [true]);

        foreach ($niveles as $nivel) {
            $dates = self::datesForFrequency($nivel['frecuencia'], $year);
            foreach ($dates as $date) {
                $codigo = 'AUTO-' . strtoupper(substr($nivel['nombre_nivel'], 0, 3)) . '-' . $date;
                $generated[] = [
                    'nivel_id' => $nivel['id'],
                    'fecha' => $date,
                    'codigo' => $codigo,
                    'frecuencia' => $nivel['frecuencia'],
                ];
            }
        }

        return $generated;
    }

    private static function datesForFrequency(string $frequency, int $year): array
    {
        $dates = [];
        $freq = strtolower($frequency);

        if (str_contains($freq, 'semanal')) {
            for ($w = 1; $w <= 52; $w++) {
                $dt = new \DateTime();
                $dt->setISODate($year, $w);
                $dates[] = $dt->format('Y-m-d');
            }
        } elseif (str_contains($freq, 'quincenal')) {
            for ($w = 1; $w <= 52; $w += 2) {
                $dt = new \DateTime();
                $dt->setISODate($year, $w);
                $dates[] = $dt->format('Y-m-d');
            }
        } elseif (str_contains($freq, 'mensual')) {
            for ($m = 1; $m <= 12; $m++) {
                $dates[] = sprintf('%d-%02d-01', $year, $m);
            }
        } elseif (str_contains($freq, 'trimestral')) {
            for ($m = 1; $m <= 12; $m += 3) {
                $dates[] = sprintf('%d-%02d-01', $year, $m);
            }
        } elseif (str_contains($freq, 'semestral')) {
            $dates[] = sprintf('%d-01-01', $year);
            $dates[] = sprintf('%d-07-01', $year);
        } elseif (str_contains($freq, 'anual')) {
            $dates[] = sprintf('%d-01-01', $year);
        }

        return $dates;
    }
}
