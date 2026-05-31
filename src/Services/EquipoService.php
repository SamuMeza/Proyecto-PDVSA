<?php
namespace App\Services;

use App\Core\Session;
use App\Models\Equipo;
use App\Models\CategoriaEquipo;
use App\Models\Zona;

class EquipoService
{
    public static function listWithFilters(): array
    {
        $family = trim($_GET['filter_family'] ?? '');
        $categoriaId = (int) ($_GET['filter_categoria'] ?? 0);
        $zonaId = (int) ($_GET['filter_zona'] ?? 0);
        $estado = $_GET['filter_estado'] ?? 'todos';

        return Equipo::listWithFilters($family, $categoriaId, $zonaId, $estado);
    }

    public static function getFormData(): array
    {
        $data = [
            'equipo_id' => '',
            'nombre' => '',
            'familia' => '',
            'categoria_id' => '',
            'zona_id' => '',
            'estado' => 'activo',
            'descripcion' => '',
        ];

        if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
            $equipo = Equipo::find((int) $_GET['edit']);
            if ($equipo) {
                $data = [
                    'equipo_id' => $equipo['id'],
                    'nombre' => $equipo['nombre'] ?? '',
                    'familia' => $equipo['grupo_responsable'] ?? '',
                    'categoria_id' => $equipo['categoria_id'] ?? '',
                    'zona_id' => $equipo['zona_id'] ?? '',
                    'estado' => $equipo['estado'] ?? 'activo',
                    'descripcion' => $equipo['descripcion'] ?? '',
                ];
            }
        }

        return $data;
    }

    public static function save(array $data, int $userId): array
    {
        $error = '';

        if (empty($data['nombre']) || empty($data['familia'])) {
            $error = 'El nombre y la familia son obligatorios.';
        }

        $categoriaId = (int) ($data['categoria_id'] ?? 0);
        if (!$categoriaId || !CategoriaEquipo::existsActive($categoriaId)) {
            $error = $error ?: 'Seleccione una categoría válida.';
        }

        $zonaId = (int) ($data['zona_id'] ?? 0);
        if (!$zonaId || !Zona::existsActive($zonaId)) {
            $error = $error ?: 'Seleccione una zona válida.';
        }

        if (($data['estado'] ?? '') === 'inactivo' && !AuthService::isAdmin()) {
            $error = $error ?: 'Solo un Administrador puede desactivar un equipo.';
        }

        if ($error) {
            return ['ok' => false, 'error' => $error];
        }

        $equipoId = $data['equipo_id'] ?? '';
        $now = date('Y-m-d H:i:s');
        $estado = in_array($data['estado'] ?? 'activo', ['activo', 'inactivo'], true) ? $data['estado'] : 'activo';

        if ($equipoId) {
            Equipo::update((int) $equipoId, [
                'nombre' => trim($data['nombre']),
                'grupo_responsable' => trim($data['familia']),
                'categoria_id' => $categoriaId,
                'zona_id' => $zonaId,
                'estado' => $estado,
                'descripcion' => trim($data['descripcion']),
                'modificado_por_usuario_id' => $userId,
                'actualizado_en' => $now,
            ]);
            return ['ok' => true, 'message' => 'Equipo actualizado correctamente.'];
        }

        Equipo::insert([
            'nombre' => trim($data['nombre']),
            'grupo_responsable' => trim($data['familia']),
            'categoria_id' => $categoriaId,
            'zona_id' => $zonaId,
            'estado' => $estado,
            'descripcion' => trim($data['descripcion']),
            'registrado_por_usuario_id' => $userId,
            'creado_en' => $now,
            'actualizado_en' => $now,
        ]);
        return ['ok' => true, 'message' => 'Equipo creado correctamente.'];
    }

    public static function toggleStatus(int $id): ?string
    {
        return Equipo::toggleStatus($id);
    }

    public static function checkAccess(): bool
    {
        $allowedRoles = ['Supervisor', 'Planificador/Programador'];
        $rolActual = Session::get('rol_nombre', '');
        return AuthService::isAdmin()
            || in_array($rolActual, $allowedRoles, true)
            || AuthService::hasPermission('equipos', 'ver');
    }

    public static function canCreate(): bool
    {
        return AuthService::isAdmin() || AuthService::hasPermission('equipos', 'crear');
    }

    public static function canEdit(): bool
    {
        return AuthService::isAdmin() || AuthService::hasPermission('equipos', 'editar');
    }

    public static function canDeactivate(): bool
    {
        return AuthService::isAdmin() || AuthService::hasPermission('equipos', 'desactivar');
    }
}
