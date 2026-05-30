<?php
namespace App\Services;

use App\Core\Session;
use App\Helpers\SecurityHelper;
use App\Models\Equipo;
use App\Models\OrdenPreventiva;
use App\Models\NivelMantenimiento;

class PreventivaService
{
    public static function listWithFilters(): array
    {
        return OrdenPreventiva::listWithFilters(
            (int) ($_GET['filter_equipo'] ?? 0),
            $_GET['filter_estado'] ?? 'todos',
            trim($_GET['filter_fecha_desde'] ?? ''),
            trim($_GET['filter_fecha_hasta'] ?? '')
        );
    }

    public static function getFormData(): array
    {
        $data = [
            'id' => '', 'codigo_unico' => '', 'equipo_id' => '',
            'nivel_mantenimiento_id' => '', 'fecha_planificada' => '',
            'hora_inicio' => '', 'hora_fin' => '', 'estado' => 'planificada',
            'planificador_id' => '', 'duracion_estimada_horas' => '',
            'descripcion' => '', 'observaciones_mantenedor' => '',
            'observaciones_supervisor' => '', 'motivo_suspension' => '',
        ];

        if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
            $orden = OrdenPreventiva::find((int) $_GET['edit']);
            if ($orden) {
                $data = [
                    'id' => $orden['id'],
                    'codigo_unico' => $orden['codigo_unico'] ?? '',
                    'equipo_id' => $orden['equipo_id'] ?? '',
                    'nivel_mantenimiento_id' => $orden['nivel_mantenimiento_id'] ?? '',
                    'fecha_planificada' => $orden['fecha_planificada'] ?? '',
                    'hora_inicio' => $orden['hora_inicio'] ?? '',
                    'hora_fin' => $orden['hora_fin'] ?? '',
                    'estado' => $orden['estado'] ?? 'planificada',
                    'planificador_id' => $orden['planificador_id'] ?? '',
                    'duracion_estimada_horas' => $orden['duracion_estimada_horas'] ?? '',
                    'descripcion' => $orden['descripcion'] ?? '',
                    'observaciones_mantenedor' => $orden['observaciones_mantenedor'] ?? '',
                    'observaciones_supervisor' => $orden['observaciones_supervisor'] ?? '',
                    'motivo_suspension' => $orden['motivo_suspension'] ?? '',
                ];
            }
        }

        return $data;
    }

    public static function save(array $data): array
    {
        $error = '';

        $data['equipo_id'] = (int) ($data['equipo_id'] ?? 0);
        $data['nivel_mantenimiento_id'] = (int) ($data['nivel_mantenimiento_id'] ?? 0);
        $data['planificador_id'] = (int) ($data['planificador_id'] ?? 0);

        if (empty($data['fecha_planificada']) || $data['equipo_id'] <= 0) {
            $error = 'La fecha planificada y el equipo son obligatorios.';
        }
        if ($data['nivel_mantenimiento_id'] <= 0) {
            $error = $error ?: 'Seleccione un nivel de mantenimiento.';
        }
        if ($data['planificador_id'] <= 0) {
            $error = $error ?: 'Seleccione un planificador responsable.';
        }

        $horaInicio = $data['hora_inicio'] ?? '';
        $horaFin = $data['hora_fin'] ?? '';
        if ($horaInicio !== '' && $horaFin !== '' && $horaInicio >= $horaFin) {
            $error = $error ?: 'La hora de inicio debe ser anterior a la hora de fin.';
        }

        if ($error) {
            return ['ok' => false, 'error' => $error];
        }

        $now = date('Y-m-d H:i:s');

        if (!empty($data['id'])) {
            OrdenPreventiva::update((int) $data['id'], [
                'equipo_id' => $data['equipo_id'],
                'nivel_mantenimiento_id' => $data['nivel_mantenimiento_id'],
                'fecha_planificada' => $data['fecha_planificada'],
                'hora_inicio' => $horaInicio ?: null,
                'hora_fin' => $horaFin ?: null,
                'estado' => in_array($data['estado'] ?? 'planificada', OrdenPreventiva::validStates(), true) ? $data['estado'] : 'planificada',
                'planificador_id' => $data['planificador_id'],
                'duracion_estimada_horas' => (float) ($data['duracion_estimada_horas'] ?? 0),
                'descripcion' => trim($data['descripcion'] ?? '') ?: null,
                'observaciones_mantenedor' => trim($data['observaciones_mantenedor'] ?? '') ?: null,
                'observaciones_supervisor' => trim($data['observaciones_supervisor'] ?? '') ?: null,
                'motivo_suspension' => trim($data['motivo_suspension'] ?? '') ?: null,
                'actualizada_en' => $now,
            ]);
            return ['ok' => true, 'message' => 'Orden preventiva actualizada correctamente.'];
        }

        $codigo = $data['codigo_unico'] ?: SecurityHelper::generateCode('PREV-');
        OrdenPreventiva::insert([
            'codigo_unico' => $codigo,
            'equipo_id' => $data['equipo_id'],
            'nivel_mantenimiento_id' => $data['nivel_mantenimiento_id'],
            'fecha_planificada' => $data['fecha_planificada'],
            'hora_inicio' => $horaInicio ?: null,
            'hora_fin' => $horaFin ?: null,
            'estado' => 'planificada',
            'planificador_id' => $data['planificador_id'],
            'duracion_estimada_horas' => (float) ($data['duracion_estimada_horas'] ?? 1),
            'descripcion' => trim($data['descripcion'] ?? '') ?: null,
            'creada_en' => $now,
            'actualizada_en' => $now,
        ]);
        return ['ok' => true, 'message' => 'Orden preventiva creada correctamente. Código: ' . htmlspecialchars($codigo)];
    }

    public static function changeState(int $id, string $newState, string $otpCode): array
    {
        $orden = OrdenPreventiva::find($id);
        if (!$orden) {
            return ['ok' => false, 'error' => 'Orden no encontrada.'];
        }

        $transitions = OrdenPreventiva::allowedTransitions($orden['estado']);
        if (!in_array($newState, $transitions, true)) {
            return ['ok' => false, 'error' => 'No se puede cambiar de "' . $orden['estado'] . '" a "' . $newState . '".'];
        }

        if (in_array($newState, ['en_curso', 'cerrada'], true) && $otpCode === '') {
            $otpEsperado = OrdenPreventiva::getOtp($id);
            if ($otpEsperado === null || $otpEsperado === '') {
                $newOtp = SecurityHelper::generateOtp();
                OrdenPreventiva::setOtp($id, $newOtp);
                return ['ok' => false, 'error' => 'Se requiere código OTP para iniciar la orden. OTP generado: ' . $newOtp];
            }
            return ['ok' => false, 'error' => 'Se requiere el código OTP para iniciar la orden. Consulte el código generado previamente.'];
        }

        if (in_array($newState, ['en_curso', 'cerrada'], true) && $otpCode !== '') {
            $otpEsperado = OrdenPreventiva::getOtp($id);
            if ($otpEsperado === null || $otpEsperado !== $otpCode) {
                return ['ok' => false, 'error' => 'Código OTP inválido. Verifique el código e intente nuevamente.'];
            }
        }

        $now = date('Y-m-d H:i:s');
        $updates = [
            'estado' => $newState,
            'codigo_otp_validacion' => null,
            'actualizada_en' => $now,
        ];

        if ($newState === 'en_curso') {
            $updates['fecha_inicio_ejecucion'] = $now;
        } elseif ($newState === 'cerrada') {
            $updates['fecha_cierre_ejecucion'] = $now;
        }

        OrdenPreventiva::update($id, $updates);
        return ['ok' => true, 'message' => 'Estado cambiado a "' . $newState . '" correctamente.'];
    }

    public static function checkAccess(): bool
    {
        $allowedRoles = ['Supervisor', 'Planificador/Programador'];
        $rolActual = Session::get('rol_nombre', '');
        return AuthService::isAdmin()
            || in_array($rolActual, $allowedRoles, true)
            || AuthService::hasPermission('preventivas', 'ver');
    }

    public static function canCreate(): bool
    {
        return AuthService::isAdmin() || AuthService::hasPermission('preventivas', 'crear');
    }

    public static function canEdit(): bool
    {
        return AuthService::isAdmin() || AuthService::hasPermission('preventivas', 'editar');
    }

    public static function canChangeState(): bool
    {
        return AuthService::isAdmin() || AuthService::hasPermission('preventivas', 'cambiar_estado');
    }
}
