<?php
namespace App\Services;

use App\Core\Database;
use App\Models\Alerta;
use App\Models\Calibracion;

class AlertGenerator
{
    public static function generateAll(): array
    {
        $generated = [];
        $generated = array_merge($generated, self::calibracionesVencidas());
        $generated = array_merge($generated, self::calibracionesProximas());
        return $generated;
    }

    private static function calibracionesVencidas(): array
    {
        $vencidas = Calibracion::findVencidas();
        $generated = [];
        foreach ($vencidas as $cal) {
            Alerta::insert([
                'usuario_destino_id' => $cal['registrado_por_usuario_id'],
                'tipo_alerta' => 'calibracion_vencida',
                'mensaje' => 'Calibración vencida para equipo #' . $cal['equipo_id'],
                'entidad_relacionada_tipo' => 'calibracion',
                'entidad_relacionada_id' => $cal['id'],
            ]);
            $generated[] = $cal['id'];
        }
        return $generated;
    }

    private static function calibracionesProximas(): array
    {
        $proximas = Calibracion::findProximas(30);
        $generated = [];
        foreach ($proximas as $cal) {
            Alerta::insert([
                'usuario_destino_id' => $cal['registrado_por_usuario_id'],
                'tipo_alerta' => 'calibracion_proxima',
                'mensaje' => 'Calibración próxima a vencer para equipo #' . $cal['equipo_id'],
                'entidad_relacionada_tipo' => 'calibracion',
                'entidad_relacionada_id' => $cal['id'],
                'fecha_vencimiento_referencia' => $cal['fecha_proxima_calibracion'],
            ]);
            $generated[] = $cal['id'];
        }
        return $generated;
    }
}
