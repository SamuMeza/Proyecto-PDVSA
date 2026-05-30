<?php
require_once __DIR__ . '/../config/autoload.php';

use App\Core\App;
use App\Core\Session;
use App\Core\Database;
use App\Services\AuthService;
use App\Services\CorrectivaService;
use App\Models\OrdenCorrectiva;
use App\Models\TipoFalla;
use App\Models\PrioridadFalla;
use App\Models\CategoriaEquipo;
use App\Models\Zona;
use App\Models\Equipo;
use App\Models\Checklist;
use App\Models\EjecucionChecklistItem;
use App\Models\FotoCorrectiva;
use App\Models\LogAuditoria;
use App\Helpers\SecurityHelper;

AuthService::requireAuth();

$pdo = Database::connection();

$puedeCrear = AuthService::isAdmin() || AuthService::hasPermission('correctivas', 'crear');
$puedeEditar = AuthService::isAdmin() || AuthService::hasPermission('correctivas', 'editar');
$puedeCambiarEstado = AuthService::isAdmin() || AuthService::hasPermission('correctivas', 'cambiar_estado');
$puedeEliminar = AuthService::isAdmin() || AuthService::hasPermission('correctivas', 'eliminar');

if (!AuthService::isAdmin() && !AuthService::hasPermission('correctivas', 'ver')) {
    header('Location: ' . App::BASE_PATH . '/public/index.php?error=sin_permiso');
    exit;
}

$tiposFalla = TipoFalla::allActive();
$prioridades = PrioridadFalla::allActive();
$categorias = CategoriaEquipo::allActive();
$zonas = Zona::allActive();
$usuariosActivos = \App\Models\User::raw("SELECT id, nombre_completo FROM usuarios WHERE estado = 'activo' ORDER BY nombre_completo");
$checklists = Checklist::raw(
    "SELECT c.id, c.nombre_checklist, nm.nombre AS nivel FROM checklists c LEFT JOIN niveles_mantenimiento nm ON nm.id = c.nivel_mantenimiento_id WHERE c.estado = 'activo' ORDER BY c.nombre_checklist"
);

$transiciones = OrdenCorrectiva::validStates();
$transicionesMap = [];
foreach ($transiciones as $estado) {
    $transicionesMap[$estado] = OrdenCorrectiva::allowedTransitions($estado);
}

function registrarAuditoria(PDO $pdo, string $accion, ?int $registroId, ?string $datosAnteriores, ?string $datosNuevos, string $descripcion): void
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $stmt = $pdo->prepare(
        'INSERT INTO logs_auditoria (usuario_id, accion, tabla_afectada, registro_afectado_id, datos_anteriores_json, datos_nuevos_json, direccion_ip, descripcion)
         VALUES (?, ?, \'ordenes_correctivas\', ?, ?, ?, ?, ?)'
    );
    $stmt->execute([Session::get('usuario_id'), $accion, $registroId, $datosAnteriores, $datosNuevos, $ip, $descripcion]);
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
        $error = 'No tiene permisos para realizar esta acción.';
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
            $error = 'Complete los campos obligatorios: equipo, tipo de falla, prioridad y descripción.';
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
                    registrarAuditoria($pdo, 'editar', (int) $formData['id'], json_encode($old), json_encode($formData), 'Orden correctiva editada');
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
                $mensaje = 'Orden correctiva creada correctamente. Código: ' . $codigoUnico;
                registrarAuditoria($pdo, 'crear', $nuevoId, null, json_encode($formData), 'Orden correctiva creada: ' . $codigoUnico);

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
            $otId = (int) ($_POST['ot_id'] ?? 0);
            registrarAuditoria($pdo, 'cambiar_estado', $otId, null, json_encode(['estado' => $_POST['nuevo_estado']]), $result['message']);
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
        $error = 'Orden inválida.';
    } else {
        $result = CorrectivaService::uploadPhoto($otId, $_FILES['foto'] ?? []);
        if ($result['ok']) {
            $mensaje = $result['message'];
            registrarAuditoria($pdo, 'subir_foto', $otId, null, null, 'Foto subida');
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
            registrarAuditoria($pdo, 'eliminar_foto', (int) $foto['orden_correctiva_id'], null, null, 'Foto eliminada (ID: ' . $fotoId . ')');
        }
    }
}

// --- Delete order ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_correctiva']) && $puedeEliminar) {
    $otId = (int) ($_POST['eliminar_correctiva'] ?? 0);
    $orden = OrdenCorrectiva::find($otId);
    if ($orden) {
        FotoCorrectiva::deleteByCorrectiva($otId);
        \App\Models\EjecucionChecklistItem::execute('DELETE FROM ejecucion_checklist_items WHERE orden_correctiva_id = ?', [$otId]);
        OrdenCorrectiva::delete($otId);
        $mensaje = 'Orden correctiva eliminada: ' . $orden['codigo_unico'];
        registrarAuditoria($pdo, 'eliminar', $otId, null, null, 'Orden eliminada: ' . $orden['codigo_unico']);
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
    $viewId = (int) ($_POST['view_id'] ?? 0);
    if ($puedeEditar && $itemId > 0) {
        $item = EjecucionChecklistItem::find($itemId);
        if ($item && (int) $item['orden_correctiva_id'] === $viewId) {
            $nuevoValor = $item['marcado_como_hecho'] ? 0 : 1;
            $fechaMarcado = $nuevoValor ? date('Y-m-d H:i:s') : null;
            EjecucionChecklistItem::update($itemId, [
                'marcado_como_hecho' => $nuevoValor,
                'fecha_marcado' => $fechaMarcado,
            ]);
            $mensaje = 'Item actualizado.';
        }
    }
    if ($viewId > 0) {
        $_GET['view'] = $viewId;
    }
    if ($viewId > 0 && $ordenDetalle === null) {
        $ordenDetalle = OrdenCorrectiva::rawOne(
            'SELECT o.*, e.nombre AS equipo_nombre, e.numero_activo_fijo
             FROM ordenes_correctivas o
             LEFT JOIN equipos e ON e.id = o.equipo_id
             WHERE o.id = ? LIMIT 1',
            [$viewId]
        );
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

$pageTitle = 'Órdenes correctivas';
$pageSlug = 'correctivas';
require __DIR__ . '/includes/layout.php';
?>
                <div class="page-header">
                    <h1 class="page-title">Órdenes correctivas</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
                <?php endif; ?>

<?php if ($ordenDetalle): ?>
                <div class="page-card">
                    <div class="page-header" style="margin-bottom:1rem;">
                        <h2>Detalle: <?= htmlspecialchars($ordenDetalle['codigo_unico']) ?></h2>
                        <a href="<?= App::BASE_PATH ?>/public/correctivas.php" class="btn btn-outline">&larr; Volver</a>
                    </div>
                    <div class="form-grid" style="grid-template-columns:1fr 1fr;">
                        <div><strong>Equipo:</strong> <?= htmlspecialchars($ordenDetalle['equipo_nombre'] ?? '') ?> (<?= htmlspecialchars($ordenDetalle['numero_activo_fijo'] ?? '') ?>)</div>
                        <div><strong>Tipo de falla:</strong> <?= htmlspecialchars($ordenDetalle['tipo_falla_nombre'] ?? '') ?></div>
                        <div><strong>Prioridad:</strong> <span style="color:<?= htmlspecialchars($ordenDetalle['prioridad_color'] ?? '#333') ?>"><?= htmlspecialchars($ordenDetalle['prioridad_nombre'] ?? '') ?></span></div>
                        <div><strong>Zona:</strong> <?= htmlspecialchars($ordenDetalle['zona_nombre'] ?? 'N/A') ?></div>
                        <div><strong>Reportado por:</strong> <?= htmlspecialchars($ordenDetalle['reportado_por_nombre'] ?? '') ?></div>
                        <div><strong>Fecha/Hora reporte:</strong> <?= htmlspecialchars($ordenDetalle['fecha_reporte'] ?? '') ?> <?= htmlspecialchars($ordenDetalle['hora_reporte'] ?? '') ?></div>
                        <div><strong>Estado:</strong> <span class="tag tag-<?= htmlspecialchars($ordenDetalle['estado']) ?>"><?= htmlspecialchars(ucfirst($ordenDetalle['estado'])) ?></span></div>
                        <div><strong>Supervisor:</strong> <?= htmlspecialchars($ordenDetalle['supervisor_nombre'] ?? 'Sin asignar') ?></div>
                        <div><strong>Mantenedor:</strong> <?= htmlspecialchars($ordenDetalle['mantenedor_nombre'] ?? 'Sin asignar') ?></div>
                        <?php if ($ordenDetalle['fecha_inicio_reparacion']): ?>
                        <div><strong>Inicio reparación:</strong> <?= htmlspecialchars($ordenDetalle['fecha_inicio_reparacion']) ?> <?= htmlspecialchars($ordenDetalle['hora_inicio_reparacion'] ?? '') ?></div>
                        <?php endif; ?>
                        <?php if ($ordenDetalle['fecha_fin_reparacion']): ?>
                        <div><strong>Fin reparación:</strong> <?= htmlspecialchars($ordenDetalle['fecha_fin_reparacion']) ?> <?= htmlspecialchars($ordenDetalle['hora_fin_reparacion'] ?? '') ?></div>
                        <?php endif; ?>
                        <?php if ($ordenDetalle['downtime_calculado_minutos']): ?>
                        <div><strong>Downtime:</strong> <?= htmlspecialchars($ordenDetalle['downtime_calculado_minutos']) ?> min</div>
                        <?php endif; ?>
                    </div>
                    <div style="margin-top:1rem;">
                        <h3>Descripción de la falla</h3>
                        <p><?= nl2br(htmlspecialchars($ordenDetalle['descripcion_falla'])) ?></p>
                    </div>
                    <?php if ($ordenDetalle['acciones_tomadas']): ?>
                    <div style="margin-top:1rem;">
                        <h3>Acciones tomadas</h3>
                        <p><?= nl2br(htmlspecialchars($ordenDetalle['acciones_tomadas'])) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($ordenDetalle['causa_raiz']): ?>
                    <div style="margin-top:1rem;">
                        <h3>Causa raíz</h3>
                        <p><?= nl2br(htmlspecialchars($ordenDetalle['causa_raiz'])) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($ordenDetalle['repuestos_utilizados']): ?>
                    <div style="margin-top:1rem;">
                        <h3>Repuestos utilizados</h3>
                        <p><?= nl2br(htmlspecialchars($ordenDetalle['repuestos_utilizados'])) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($transicionesMap[$ordenDetalle['estado']]) && !empty($transicionesMap[$ordenDetalle['estado']]) && $puedeCambiarEstado): ?>
                    <div style="margin-top:1.5rem; border-top:1px solid var(--border); padding-top:1rem;">
                        <h3>Cambiar estado</h3>
                        <form method="post" style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                            <input type="hidden" name="cambiar_estado" value="1">
                            <input type="hidden" name="ot_id" value="<?= $ordenDetalle['id'] ?>">
                            <select name="nuevo_estado" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($transicionesMap[$ordenDetalle['estado']] as $estado): ?>
                                    <option value="<?= htmlspecialchars($estado) ?>"><?= htmlspecialchars(ucfirst($estado)) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-primary" onclick="return confirm('¿Cambiar estado a la orden?')">Cambiar</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="page-card">
                    <h3>Fotos (<?= count($fotos) ?>/3)</h3>
                    <?php if ($puedeCrear || $puedeEditar): ?>
                    <?php if (count($fotos) < 3): ?>
                    <form method="post" enctype="multipart/form-data" style="margin-bottom:1rem;">
                        <input type="hidden" name="subir_foto" value="1">
                        <input type="hidden" name="ot_id" value="<?= $ordenDetalle['id'] ?>">
                        <div class="form-group">
                            <input type="file" name="foto" accept=".jpg,.jpeg,.png" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Subir foto</button>
                    </form>
                    <?php else: ?>
                    <p><em>Límite de 3 fotos alcanzado.</em></p>
                    <?php endif; ?>
                    <?php endif; ?>
                    <div style="display:flex; gap:1rem; flex-wrap:wrap;">
                        <?php foreach ($fotos as $foto): ?>
                            <div style="position:relative; border:1px solid var(--border); border-radius:8px; overflow:hidden; max-width:200px;">
                                <a href="<?= App::BASE_PATH ?>/public/assets/uploads/fotos-fallas/<?= htmlspecialchars($foto['ruta_archivo']) ?>" target="_blank">
                                    <img src="<?= App::BASE_PATH ?>/public/assets/uploads/fotos-fallas/<?= htmlspecialchars($foto['ruta_archivo']) ?>" alt="Foto" style="width:100%; display:block;">
                                </a>
                                <div style="padding:4px 8px; font-size:0.85em;">
                                    <?= htmlspecialchars($foto['nombre_original'] ?? '') ?> (<?= $foto['tamano_kb'] ?> KB)
                                    <?php if ($puedeEliminar): ?>
                                    <form method="post" style="display:inline;" onsubmit="return confirm('¿Eliminar esta foto?')">
                                        <input type="hidden" name="eliminar_foto" value="1">
                                        <input type="hidden" name="foto_id" value="<?= $foto['id'] ?>">
                                        <button type="submit" style="background:none;border:none;color:var(--danger);cursor:pointer;text-decoration:underline;">Eliminar</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($fotos)): ?>
                            <p>No hay fotos asociadas.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="page-card">
                    <h3>Checklist de ejecución</h3>
                    <?php if (!empty($checklistEjecuciones)): ?>
                    <table class="data-table">
                        <thead><tr>
                            <th>Item</th><th>Requerido</th><th>Estado</th><th>Fecha</th>
                            <?php if ($puedeEditar): ?><th>Acción</th><?php endif; ?>
                        </tr></thead>
                        <tbody>
                            <?php foreach ($checklistEjecuciones as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['item_descripcion'] ?? 'Item #' . $item['checklist_item_id']) ?></td>
                                <td><?= $item['es_requerido'] ? 'Sí' : 'No' ?></td>
                                <td><?= $item['marcado_como_hecho'] ? '✓ Hecho' : '— Pendiente' ?></td>
                                <td><?= $item['fecha_marcado'] ?? '—' ?></td>
                                <?php if ($puedeEditar): ?>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="toggle_checklist_item" value="1">
                                        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="view_id" value="<?= $ordenDetalle['id'] ?>">
                                        <button type="submit" class="btn btn-outline small-action"><?= $item['marcado_como_hecho'] ? 'Desmarcar' : 'Marcar' ?></button>
                                    </form>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p>No hay checklist asociado a esta orden.</p>
                    <?php endif; ?>
                </div>

                <div class="page-card">
                    <h3>Historial de auditoría</h3>
                    <?php if (!empty($auditorias)): ?>
                    <table class="data-table">
                        <thead><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Descripción</th></tr></thead>
                        <tbody>
                            <?php foreach ($auditorias as $log): ?>
                            <tr>
                                <td><?= htmlspecialchars($log['fecha_hora']) ?></td>
                                <td><?= htmlspecialchars($log['usuario_nombre'] ?? 'Sistema') ?></td>
                                <td><span class="tag tag-<?= htmlspecialchars($log['accion']) ?>"><?= htmlspecialchars($log['accion']) ?></span></td>
                                <td><?= htmlspecialchars($log['descripcion'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p>No hay eventos registrados.</p>
                    <?php endif; ?>
                </div>
<?php else: ?>
                <div class="page-card">
                    <form method="get" class="filter-panel" aria-label="Filtrar órdenes correctivas">
                        <div class="form-group">
                            <label for="filter_tipo_falla">Tipo de falla</label>
                            <select id="filter_tipo_falla" name="filter_tipo_falla">
                                <option value="0">Todas</option>
                                <?php foreach ($tiposFalla as $tf): ?>
                                    <option value="<?= $tf['id'] ?>" <?= $filterTipoFalla === (int) $tf['id'] ? 'selected' : '' ?>><?= htmlspecialchars($tf['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_prioridad">Prioridad</label>
                            <select id="filter_prioridad" name="filter_prioridad">
                                <option value="0">Todas</option>
                                <?php foreach ($prioridades as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $filterPrioridad === (int) $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_zona">Zona</label>
                            <select id="filter_zona" name="filter_zona">
                                <option value="0">Todas</option>
                                <?php foreach ($zonas as $z): ?>
                                    <option value="<?= $z['id'] ?>" <?= $filterZona === (int) $z['id'] ? 'selected' : '' ?>><?= htmlspecialchars($z['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_estado">Estado</label>
                            <select id="filter_estado" name="filter_estado">
                                <option value="todos" <?= $filterEstado === 'todos' ? 'selected' : '' ?>>Todos</option>
                                <option value="reportada" <?= $filterEstado === 'reportada' ? 'selected' : '' ?>>Reportada</option>
                                <option value="en_progreso" <?= $filterEstado === 'en_progreso' ? 'selected' : '' ?>>En progreso</option>
                                <option value="completada" <?= $filterEstado === 'completada' ? 'selected' : '' ?>>Completada</option>
                                <option value="cerrada" <?= $filterEstado === 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
                                <option value="cancelada" <?= $filterEstado === 'cancelada' ? 'selected' : '' ?>>Cancelada</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_equipo">Equipo</label>
                            <select id="filter_equipo" name="filter_equipo">
                                <option value="0">Todos</option>
                                <?php $equiposList = Equipo::allActive(); ?>
                                <?php foreach ($equiposList as $eq): ?>
                                    <option value="<?= $eq['id'] ?>" <?= $filterEquipo === (int) $eq['id'] ? 'selected' : '' ?>><?= htmlspecialchars($eq['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="align-self:end;">
                            <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                        </div>
                    </form>

                    <?php if ($puedeCrear): ?>
                        <p><a href="#formulario-correctiva" class="btn btn-primary">Crear nueva orden correctiva</a></p>
                    <?php endif; ?>

                    <table class="data-table">
                        <thead><tr>
                            <th>Código</th><th>Equipo</th><th>Tipo de falla</th><th>Prioridad</th><th>Zona</th><th>Fecha</th><th>Estado</th><th>Acciones</th>
                        </tr></thead>
                        <tbody>
                            <?php if (empty($ordenes)): ?>
                                <tr><td colspan="8">No se encontraron órdenes correctivas con los filtros seleccionados.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($ordenes as $orden): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($orden['codigo_unico']) ?></strong></td>
                                    <td><?= htmlspecialchars($orden['equipo_nombre'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($orden['tipo_falla'] ?? '') ?></td>
                                    <td style="color:<?= htmlspecialchars($orden['prioridad_color'] ?? '#333') ?>"><?= htmlspecialchars($orden['prioridad'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($orden['zona'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($orden['fecha_reporte']) ?><br><small><?= htmlspecialchars($orden['hora_reporte']) ?></small></td>
                                    <td><span class="tag tag-<?= htmlspecialchars($orden['estado']) ?>"><?= htmlspecialchars(ucfirst($orden['estado'])) ?></span></td>
                                    <td>
                                        <a href="<?= App::BASE_PATH ?>/public/correctivas.php?view=<?= $orden['id'] ?>" class="btn btn-outline small-action">Ver</a>
                                        <?php if ($puedeEditar && $orden['estado'] !== 'cerrada'): ?>
                                            <a href="<?= App::BASE_PATH ?>/public/correctivas.php?edit=<?= $orden['id'] ?>" class="btn btn-outline small-action">Editar</a>
                                        <?php endif; ?>
                                        <?php if ($puedeEliminar): ?>
                                            <form method="post" style="display:inline-block;" onsubmit="return confirm('¿Eliminar permanentemente la orden?');">
                                                <input type="hidden" name="eliminar_correctiva" value="<?= $orden['id'] ?>">
                                                <button type="submit" class="btn btn-outline small-action">Eliminar</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="page-card" id="formulario-correctiva">
                    <h2><?= $formData['id'] ? 'Editar orden correctiva' : 'Crear orden correctiva' ?></h2>
                    <form method="post" class="form-grid">
                        <input type="hidden" name="save_correctiva" value="1">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($formData['id']) ?>">

                        <div class="form-group">
                            <label for="equipo_id">Equipo</label>
                            <select id="equipo_id" name="equipo_id" required>
                                <option value="">Seleccione</option>
                                <?php $equiposForm = Equipo::allActive(); ?>
                                <?php foreach ($equiposForm as $eq): ?>
                                    <option value="<?= $eq['id'] ?>" <?= $formData['equipo_id'] == $eq['id'] ? 'selected' : '' ?>><?= htmlspecialchars($eq['nombre']) ?> (<?= htmlspecialchars($eq['numero_activo_fijo'] ?? '') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipo_falla_id">Tipo de falla</label>
                            <select id="tipo_falla_id" name="tipo_falla_id" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($tiposFalla as $tf): ?>
                                    <option value="<?= $tf['id'] ?>" <?= $formData['tipo_falla_id'] == $tf['id'] ? 'selected' : '' ?>><?= htmlspecialchars($tf['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="prioridad_id">Prioridad</label>
                            <select id="prioridad_id" name="prioridad_id" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($prioridades as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $formData['prioridad_id'] == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="zona_id">Zona</label>
                            <select id="zona_id" name="zona_id">
                                <option value="">Seleccione</option>
                                <?php foreach ($zonas as $z): ?>
                                    <option value="<?= $z['id'] ?>" <?= $formData['zona_id'] == $z['id'] ? 'selected' : '' ?>><?= htmlspecialchars($z['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="supervisor_asigno_id">Supervisor asignado</label>
                            <select id="supervisor_asigno_id" name="supervisor_asigno_id">
                                <option value="">Sin asignar</option>
                                <?php foreach ($usuariosActivos as $u): ?>
                                    <option value="<?= $u['id'] ?>" <?= $formData['supervisor_asigno_id'] == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['nombre_completo']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="mantenedor_id">Mantenedor asignado</label>
                            <select id="mantenedor_id" name="mantenedor_id">
                                <option value="">Sin asignar</option>
                                <?php foreach ($usuariosActivos as $u): ?>
                                    <option value="<?= $u['id'] ?>" <?= $formData['mantenedor_id'] == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['nombre_completo']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="checklist_id">Checklist asociado (solo al crear)</label>
                            <select id="checklist_id" name="checklist_id" <?= $formData['id'] ? 'disabled' : '' ?>>
                                <option value="">Ninguno</option>
                                <?php foreach ($checklists as $cl): ?>
                                    <option value="<?= $cl['id'] ?>"><?= htmlspecialchars($cl['nombre_checklist']) ?> (<?= htmlspecialchars($cl['nivel'] ?? '') ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($formData['id']): ?>
                                <input type="hidden" name="checklist_id" value="">
                            <?php endif; ?>
                        </div>
                        <div class="form-group" style="grid-column:1 / -1;">
                            <label for="descripcion_falla">Descripción de la falla</label>
                            <textarea id="descripcion_falla" name="descripcion_falla" rows="4" required><?= htmlspecialchars($formData['descripcion_falla']) ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column:1 / -1;">
                            <label for="acciones_tomadas">Acciones tomadas</label>
                            <textarea id="acciones_tomadas" name="acciones_tomadas" rows="3"><?= htmlspecialchars($formData['acciones_tomadas']) ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column:1 / -1;">
                            <label for="causa_raiz">Causa raíz</label>
                            <textarea id="causa_raiz" name="causa_raiz" rows="2"><?= htmlspecialchars($formData['causa_raiz']) ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column:1 / -1;">
                            <label for="repuestos_utilizados">Repuestos utilizados</label>
                            <textarea id="repuestos_utilizados" name="repuestos_utilizados" rows="2"><?= htmlspecialchars($formData['repuestos_utilizados']) ?></textarea>
                        </div>
                        <div class="form-group" style="grid-column:1 / -1; text-align:right;">
                            <button type="submit" class="btn btn-primary"><?= $formData['id'] ? 'Guardar cambios' : 'Crear orden correctiva' ?></button>
                        </div>
                    </form>
                </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/layout_footer.php'; ?>
