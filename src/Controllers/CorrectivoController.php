<?php
namespace App\Controllers;

use App\Core\App;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Services\AuthService;
use App\Services\CorrectivaService;
use App\Models\OrdenCorrectiva;
use App\Models\TipoFalla;
use App\Models\PrioridadFalla;
use App\Models\CategoriaEquipo;
use App\Models\Zona;
use App\Models\Equipo;
use App\Models\User;
use App\Models\Checklist;
use App\Models\EjecucionChecklistItem;
use App\Models\FotoCorrectiva;
use App\Models\LogAuditoria;

class CorrectivoController
{
    public function index(): void
    {
        AuthService::requireAuth();

        if (!AuthService::isAdmin() && !AuthService::hasPermission('correctivas', 'ver')) {
            Response::redirect(App::BASE_PATH . '/?error=sin_permiso');
        }

        $puedeCrear = AuthService::isAdmin() || AuthService::hasPermission('correctivas', 'crear');
        $puedeEditar = AuthService::isAdmin() || AuthService::hasPermission('correctivas', 'editar');
        $puedeCambiarEstado = AuthService::isAdmin() || AuthService::hasPermission('correctivas', 'cambiar_estado');
        $puedeEliminar = AuthService::isAdmin() || AuthService::hasPermission('correctivas', 'eliminar');

        $tiposFalla = TipoFalla::allActive();
        $prioridades = PrioridadFalla::allActive();
        $categorias = CategoriaEquipo::allActive();
        $zonas = Zona::allActive();
        $usuariosActivos = User::raw("SELECT id, nombre_completo FROM usuarios WHERE estado = 'activo' ORDER BY nombre_completo");
        $checklists = Checklist::raw(
            "SELECT c.id, c.nombre_checklist, nm.nombre_nivel AS nivel FROM checklists c LEFT JOIN niveles_mantenimiento nm ON nm.id = c.nivel_mantenimiento_id WHERE c.estado = 'activo' ORDER BY c.nombre_checklist"
        );

        $transiciones = OrdenCorrectiva::validStates();
        $transicionesMap = [];
        foreach ($transiciones as $estado) {
            $transicionesMap[$estado] = OrdenCorrectiva::allowedTransitions($estado);
        }

        $error = '';
        $mensaje = '';
        $formData = [
            'id' => '', 'equipo_id' => '', 'tipo_falla_id' => '', 'prioridad_id' => '',
            'zona_id' => '', 'descripcion_falla' => '', 'acciones_tomadas' => '',
            'causa_raiz' => '', 'repuestos_utilizados' => '', 'supervisor_asigno_id' => '',
            'mantenedor_id' => '', 'checklist_id' => '',
        ];

        // --- Save / Update ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_correctiva'])) {
            if (!$puedeCrear && !$puedeEditar) {
                $error = 'No tiene permisos para realizar esta accion.';
            } else {
                $formData = [
                    'id' => $_POST['id'] ?? '',
                    'equipo_id' => (int) ($_POST['equipo_id'] ?? 0),
                    'tipo_falla_id' => (int) ($_POST['tipo_falla_id'] ?? 0),
                    'prioridad_id' => (int) ($_POST['prioridad_id'] ?? 0),
                    'zona_id' => (int) ($_POST['zona_id'] ?? 0) ?: null,
                    'descripcion_falla' => trim($_POST['descripcion_falla'] ?? ''),
                    'acciones_tomadas' => trim($_POST['acciones_tomadas'] ?? ''),
                    'causa_raiz' => trim($_POST['causa_raiz'] ?? ''),
                    'repuestos_utilizados' => trim($_POST['repuestos_utilizados'] ?? ''),
                    'supervisor_asigno_id' => (int) ($_POST['supervisor_asigno_id'] ?? 0) ?: null,
                    'mantenedor_id' => (int) ($_POST['mantenedor_id'] ?? 0) ?: null,
                ];

                if ($formData['equipo_id'] === 0 || $formData['tipo_falla_id'] === 0 || $formData['prioridad_id'] === 0 || $formData['descripcion_falla'] === '') {
                    $error = 'Complete los campos obligatorios: equipo, tipo de falla, prioridad y descripcion.';
                }

                if (!$error) {
                    $usuarioId = (int) Session::get('usuario_id');
                    if ($formData['id']) {
                        $old = OrdenCorrectiva::find((int) $formData['id']);
                        if (!$old) {
                            $error = 'La orden no existe.';
                        } else {
                            OrdenCorrectiva::update((int) $formData['id'], [
                                'equipo_id' => $formData['equipo_id'],
                                'tipo_falla_id' => $formData['tipo_falla_id'],
                                'prioridad_id' => $formData['prioridad_id'],
                                'zona_id' => $formData['zona_id'],
                                'descripcion_falla' => $formData['descripcion_falla'],
                                'acciones_tomadas' => $formData['acciones_tomadas'],
                                'causa_raiz' => $formData['causa_raiz'],
                                'repuestos_utilizados' => $formData['repuestos_utilizados'],
                                'supervisor_asigno_id' => $formData['supervisor_asigno_id'],
                                'mantenedor_id' => $formData['mantenedor_id'],
                                'actualizado_en' => date('Y-m-d H:i:s'),
                            ]);
                            $mensaje = 'Orden correctiva actualizada correctamente.';
                            $this->registrarAuditoria('editar', (int) $formData['id'], json_encode($old), json_encode($formData), 'Orden correctiva editada');
                            $formData['id'] = '';
                        }
                    } else {
                        $codigoUnico = 'OC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
                        $nuevoId = OrdenCorrectiva::insert([
                            'codigo_unico' => $codigoUnico,
                            'equipo_id' => $formData['equipo_id'],
                            'tipo_falla_id' => $formData['tipo_falla_id'],
                            'prioridad_id' => $formData['prioridad_id'],
                            'zona_id' => $formData['zona_id'],
                            'fecha_reporte' => date('Y-m-d'),
                            'hora_reporte' => date('H:i:s'),
                            'reportado_por_usuario_id' => $usuarioId,
                            'descripcion_falla' => $formData['descripcion_falla'],
                            'acciones_tomadas' => $formData['acciones_tomadas'],
                            'causa_raiz' => $formData['causa_raiz'],
                            'repuestos_utilizados' => $formData['repuestos_utilizados'],
                            'supervisor_asigno_id' => $formData['supervisor_asigno_id'],
                            'mantenedor_id' => $formData['mantenedor_id'],
                            'estado' => 'reportada',
                            'creada_en' => date('Y-m-d H:i:s'),
                            'actualizado_en' => date('Y-m-d H:i:s'),
                        ]);
                        $mensaje = 'Orden correctiva creada correctamente. Codigo: ' . $codigoUnico;
                        $this->registrarAuditoria('crear', $nuevoId, null, json_encode($formData), 'Orden correctiva creada: ' . $codigoUnico);

                        if (!empty($_POST['checklist_id'])) {
                            $checklistId = (int) $_POST['checklist_id'];
                            $items = \App\Models\ChecklistItem::findByChecklist($checklistId);
                            foreach ($items as $item) {
                                EjecucionChecklistItem::insert([
                                    'orden_correctiva_id' => $nuevoId,
                                    'checklist_item_id' => $item['id'],
                                ]);
                            }
                            if (!empty($items)) {
                                $mensaje .= ' Checklist asociado.';
                            }
                        }

                        $formData = [
                            'id' => '', 'equipo_id' => '', 'tipo_falla_id' => '', 'prioridad_id' => '',
                            'zona_id' => '', 'descripcion_falla' => '', 'acciones_tomadas' => '',
                            'causa_raiz' => '', 'repuestos_utilizados' => '', 'supervisor_asigno_id' => '',
                            'mantenedor_id' => '', 'checklist_id' => '',
                        ];
                    }
                }
            }
        }

        // --- State transitions ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_estado'])) {
            if (!$puedeCambiarEstado) {
                $error = 'No tiene permisos para cambiar el estado.';
            } else {
                $result = CorrectivaService::changeState(
                    (int) ($_POST['ot_id'] ?? 0),
                    $_POST['nuevo_estado'] ?? '',
                    trim($_POST['codigo_otp'] ?? '')
                );
                if ($result['ok']) {
                    $mensaje = $result['message'];
                    $this->registrarAuditoria('cambiar_estado', (int) ($_POST['ot_id'] ?? 0), null, json_encode(['estado' => $_POST['nuevo_estado']]), $result['message']);
                } else {
                    $error = $result['error'];
                }
            }
        }

        // --- Photo upload ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subir_foto'])) {
            $otId = (int) ($_POST['ot_id'] ?? 0);
            if (!$puedeCrear && !$puedeEditar) {
                $error = 'No tiene permisos para subir fotos.';
            } elseif ($otId <= 0) {
                $error = 'Orden invalida.';
            } else {
                $result = CorrectivaService::uploadPhoto($otId, $_FILES['foto'] ?? []);
                if ($result['ok']) {
                    $mensaje = $result['message'];
                    $this->registrarAuditoria('subir_foto', $otId, null, null, 'Foto subida');
                } else {
                    $error = $result['error'];
                }
            }
        }

        // --- Delete photo ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_foto']) && $puedeEliminar) {
            $fotoId = (int) ($_POST['foto_id'] ?? 0);
            $foto = FotoCorrectiva::find($fotoId);
            if ($foto) {
                $result = CorrectivaService::deletePhoto($fotoId, (int) $foto['orden_correctiva_id']);
                if ($result['ok']) {
                    $mensaje = $result['message'];
                    $this->registrarAuditoria('eliminar_foto', (int) $foto['orden_correctiva_id'], null, 'Foto eliminada (ID: ' . $fotoId . ')');
                }
            }
        }

        // --- Delete order ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_correctiva']) && $puedeEliminar) {
            $otId = (int) ($_POST['eliminar_correctiva'] ?? 0);
            $orden = OrdenCorrectiva::find($otId);
            if ($orden) {
                FotoCorrectiva::deleteByCorrectiva($otId);
                EjecucionChecklistItem::execute('DELETE FROM ejecucion_checklist_items WHERE orden_correctiva_id = ?', [$otId]);
                OrdenCorrectiva::delete($otId);
                $mensaje = 'Orden correctiva eliminada: ' . $orden['codigo_unico'];
                $this->registrarAuditoria('eliminar', $otId, null, 'Orden eliminada: ' . $orden['codigo_unico']);
            }
        }

        // --- Edit pre-fill ---
        if (isset($_GET['edit']) && is_numeric($_GET['edit']) && $puedeEditar) {
            $orden = OrdenCorrectiva::find((int) $_GET['edit']);
            if ($orden) {
                $formData = [
                    'id' => $orden['id'],
                    'equipo_id' => $orden['equipo_id'],
                    'tipo_falla_id' => $orden['tipo_falla_id'],
                    'prioridad_id' => $orden['prioridad_id'],
                    'zona_id' => $orden['zona_id'] ?? '',
                    'descripcion_falla' => $orden['descripcion_falla'],
                    'acciones_tomadas' => $orden['acciones_tomadas'] ?? '',
                    'causa_raiz' => $orden['causa_raiz'] ?? '',
                    'repuestos_utilizados' => $orden['repuestos_utilizados'] ?? '',
                    'supervisor_asigno_id' => $orden['supervisor_asigno_id'] ?? '',
                    'mantenedor_id' => $orden['mantenedor_id'] ?? '',
                ];
            }
        }

        // --- View detail ---
        $ordenDetalle = null;
        $fotos = [];
        $auditorias = [];
        $checklistEjecuciones = [];
        if (isset($_GET['view']) && is_numeric($_GET['view'])) {
            $viewId = (int) $_GET['view'];
            $ordenDetalle = OrdenCorrectiva::rawOne(
                'SELECT o.*, e.nombre AS equipo_nombre, e.numero_activo_fijo, tf.nombre AS tipo_falla_nombre, p.nombre AS prioridad_nombre, p.color_alert,
                        z.nombre AS zona_nombre, r.nombre_completo AS reportado_por_nombre,
                        s.nombre_completo AS supervisor_nombre, m.nombre_completo AS mantenedor_nombre
                 FROM ordenes_correctivas o
                 LEFT JOIN equipos e ON e.id = o.equipo_id
                 LEFT JOIN tipos_falla tf ON tf.id = o.tipo_falla_id
                 LEFT JOIN prioridades_falla p ON p.id = o.prioridad_id
                 LEFT JOIN zonas z ON z.id = o.zona_id
                 LEFT JOIN usuarios r ON r.id = o.reportado_por_usuario_id
                 LEFT JOIN usuarios s ON s.id = o.supervisor_asigno_id
                 LEFT JOIN usuarios m ON m.id = o.mantenedor_id
                 WHERE o.id = ? LIMIT 1',
                [$viewId]
            );
            if ($ordenDetalle) {
                $fotos = FotoCorrectiva::findByCorrectiva($viewId);
                $auditorias = LogAuditoria::raw(
                    'SELECT l.*, u.nombre_completo AS usuario_nombre
                     FROM logs_auditoria l
                     LEFT JOIN usuarios u ON u.id = l.usuario_id
                     WHERE l.tabla_afectada = \'ordenes_correctivas\' AND l.registro_afectado_id = ?
                     ORDER BY l.fecha_hora DESC',
                    [$viewId]
                );
                $checklistEjecuciones = EjecucionChecklistItem::raw(
                    'SELECT eci.*, ci.descripcion AS item_descripcion, ci.es_requerido
                     FROM ejecucion_checklist_items eci
                     LEFT JOIN checklist_items ci ON ci.id = eci.checklist_item_id
                     WHERE eci.orden_correctiva_id = ?
                     ORDER BY ci.orden ASC, ci.id ASC',
                    [$viewId]
                );
            }
        }

        // --- Checklist item toggle ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_checklist_item'])) {
            $itemId = (int) ($_POST['item_id'] ?? 0);
            $viewIdPost = (int) ($_POST['view_id'] ?? 0);
            if ($puedeEditar && $itemId > 0) {
                $item = EjecucionChecklistItem::find($itemId);
                if ($item && (int) $item['orden_correctiva_id'] === $viewIdPost) {
                    $nuevoValor = $item['marcado_como_hecho'] ? 0 : 1;
                    $fechaMarcado = $nuevoValor ? date('Y-m-d H:i:s') : null;
                    EjecucionChecklistItem::update($itemId, [
                        'marcado_como_hecho' => $nuevoValor,
                        'fecha_marcado' => $fechaMarcado,
                    ]);
                    $mensaje = 'Item actualizado.';
                }
            }
            if ($viewIdPost > 0 && $ordenDetalle === null) {
                $ordenDetalle = OrdenCorrectiva::rawOne(
                    'SELECT o.*, e.nombre AS equipo_nombre, e.numero_activo_fijo
                     FROM ordenes_correctivas o
                     LEFT JOIN equipos e ON e.id = o.equipo_id
                     WHERE o.id = ? LIMIT 1',
                    [$viewIdPost]
                );
                if ($ordenDetalle) {
                    $fotos = FotoCorrectiva::findByCorrectiva($viewIdPost);
                    $auditorias = LogAuditoria::raw(
                        'SELECT l.*, u.nombre_completo AS usuario_nombre
                         FROM logs_auditoria l
                         LEFT JOIN usuarios u ON u.id = l.usuario_id
                         WHERE l.tabla_afectada = \'ordenes_correctivas\' AND l.registro_afectado_id = ?
                         ORDER BY l.fecha_hora DESC',
                        [$viewIdPost]
                    );
                    $checklistEjecuciones = EjecucionChecklistItem::raw(
                        'SELECT eci.*, ci.descripcion AS item_descripcion, ci.es_requerido
                         FROM ejecucion_checklist_items eci
                         LEFT JOIN checklist_items ci ON ci.id = eci.checklist_item_id
                         WHERE eci.orden_correctiva_id = ?
                         ORDER BY ci.orden ASC, ci.id ASC',
                        [$viewIdPost]
                    );
                }
            }
        }

        // --- Filters ---
        $filterTipoFalla = (int) ($_GET['filter_tipo_falla'] ?? 0);
        $filterPrioridad = (int) ($_GET['filter_prioridad'] ?? 0);
        $filterZona = (int) ($_GET['filter_zona'] ?? 0);
        $filterEstado = in_array($_GET['filter_estado'] ?? '', array_merge($transiciones, ['todos']), true) ? $_GET['filter_estado'] : 'todos';
        $filterEquipo = (int) ($_GET['filter_equipo'] ?? 0);

        $where = ['1 = 1'];
        $params = [];
        if ($filterTipoFalla > 0) { $where[] = 'o.tipo_falla_id = ?'; $params[] = $filterTipoFalla; }
        if ($filterPrioridad > 0) { $where[] = 'o.prioridad_id = ?'; $params[] = $filterPrioridad; }
        if ($filterZona > 0) { $where[] = 'o.zona_id = ?'; $params[] = $filterZona; }
        if ($filterEstado !== 'todos') { $where[] = 'o.estado = ?'; $params[] = $filterEstado; }
        if ($filterEquipo > 0) { $where[] = 'o.equipo_id = ?'; $params[] = $filterEquipo; }

        $ordenes = OrdenCorrectiva::raw(
            'SELECT o.id, o.codigo_unico, o.fecha_reporte, o.hora_reporte, o.estado, o.descripcion_falla,
                    e.nombre AS equipo_nombre, tf.nombre AS tipo_falla, p.nombre AS prioridad, p.color_alert AS prioridad_color,
                    z.nombre AS zona, u.nombre_completo AS reportado_por
             FROM ordenes_correctivas o
             LEFT JOIN equipos e ON e.id = o.equipo_id
             LEFT JOIN tipos_falla tf ON tf.id = o.tipo_falla_id
             LEFT JOIN prioridades_falla p ON p.id = o.prioridad_id
             LEFT JOIN zonas z ON z.id = o.zona_id
             LEFT JOIN usuarios u ON u.id = o.reportado_por_usuario_id
             WHERE ' . implode(' AND ', $where) . '
             ORDER BY o.fecha_reporte DESC, o.hora_reporte DESC',
            $params
        );

        Response::view('correctivo/index', [
            'puedeCrear' => $puedeCrear,
            'puedeEditar' => $puedeEditar,
            'puedeCambiarEstado' => $puedeCambiarEstado,
            'puedeEliminar' => $puedeEliminar,
            'tiposFalla' => $tiposFalla,
            'prioridades' => $prioridades,
            'categorias' => $categorias,
            'zonas' => $zonas,
            'usuariosActivos' => $usuariosActivos,
            'checklists' => $checklists,
            'transiciones' => $transiciones,
            'transicionesMap' => $transicionesMap,
            'error' => $error,
            'mensaje' => $mensaje,
            'formData' => $formData,
            'ordenDetalle' => $ordenDetalle,
            'fotos' => $fotos,
            'auditorias' => $auditorias,
            'checklistEjecuciones' => $checklistEjecuciones,
            'ordenes' => $ordenes,
            'filterTipoFalla' => $filterTipoFalla,
            'filterPrioridad' => $filterPrioridad,
            'filterZona' => $filterZona,
            'filterEstado' => $filterEstado,
            'filterEquipo' => $filterEquipo,
            'pageTitle' => 'Ordenes Correctivas',
            'pageSlug' => 'correctivas',
        ]);
    }

    private function registrarAuditoria(string $accion, ?int $registroId, ?string $datosAnteriores, ?string $datosNuevos, string $descripcion): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $stmt = Database::connection()->prepare(
            'INSERT INTO logs_auditoria (usuario_id, accion, tabla_afectada, registro_afectado_id, datos_anteriores_json, datos_nuevos_json, direccion_ip, descripcion)
             VALUES (?, ?, \'ordenes_correctivas\', ?, ?, ?, ?, ?)'
        );
        $stmt->execute([Session::get('usuario_id'), $accion, $registroId, $datosAnteriores, $datosNuevos, $ip, $descripcion]);
    }
}
