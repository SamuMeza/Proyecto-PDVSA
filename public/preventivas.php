<?php
require_once __DIR__ . '/../auth/auth_functions.php';
requerirAutenticacion();

$allowedRoles = ['Supervisor', 'Planificador/Programador'];
$rolActual = $_SESSION['rol_nombre'] ?? '';
$accesoPermitido = esAdministrador() || in_array($rolActual, $allowedRoles, true) || tienePermiso('preventivas', 'ver');
if (!$accesoPermitido) {
    header('Location: ' . BASE_PATH . '/public/index.php?error=sin_permiso');
    exit;
}

$pdo = getDbConnection();
$equipos = $pdo->query("SELECT id, nombre, numero_activo_fijo FROM equipos WHERE estado = 'activo' ORDER BY nombre")->fetchAll();
$categorias = $pdo->query("SELECT id, nombre FROM categorias_equipo WHERE estado = 'activo' ORDER BY nombre")->fetchAll();
$planificadores = $pdo->query("SELECT u.id, u.nombre_completo FROM usuarios u INNER JOIN roles r ON r.id = u.rol_id WHERE u.estado = 'activo' ORDER BY u.nombre_completo")->fetchAll();
$nivelesMantenimiento = $pdo->query("SELECT id, nombre FROM niveles_mantenimiento WHERE estado = 'activo' ORDER BY nombre")->fetchAll();

$puedeCrear = esAdministrador() || tienePermiso('preventivas', 'crear');
$puedeEditar = esAdministrador() || tienePermiso('preventivas', 'editar');
$puedeCambiarEstado = esAdministrador() || tienePermiso('preventivas', 'cambiar_estado');

$error = '';
$mensaje = '';
$formData = [
    'id' => '',
    'codigo_unico' => '',
    'equipo_id' => '',
    'nivel_mantenimiento_id' => '',
    'fecha_planificada' => '',
    'hora_inicio' => '',
    'hora_fin' => '',
    'estado' => 'planificada',
    'planificador_id' => '',
    'duracion_estimada_horas' => '',
    'descripcion' => '',
    'observaciones_mantenedor' => '',
    'observaciones_supervisor' => '',
    'motivo_suspension' => '',
];

function generarCodigoUnico(): string
{
    return 'PREV-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
}

function estadosValidos(): array
{
    return ['planificada', 'en_curso', 'cerrada', 'suspendida'];
}

function transicionesPermitidas(string $estadoActual): array
{
    $mapa = [
        'planificada' => ['en_curso', 'suspendida'],
        'en_curso'    => ['cerrada', 'suspendida'],
        'cerrada'     => [],
        'suspendida'  => ['planificada'],
    ];
    return $mapa[$estadoActual] ?? [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_preventiva'])) {
    $formData = [
        'id' => $_POST['id'] ?? '',
        'codigo_unico' => trim($_POST['codigo_unico'] ?? ''),
        'equipo_id' => (int) ($_POST['equipo_id'] ?? 0),
        'nivel_mantenimiento_id' => (int) ($_POST['nivel_mantenimiento_id'] ?? 0),
        'fecha_planificada' => trim($_POST['fecha_planificada'] ?? ''),
        'hora_inicio' => trim($_POST['hora_inicio'] ?? ''),
        'hora_fin' => trim($_POST['hora_fin'] ?? ''),
        'estado' => in_array($_POST['estado'] ?? 'planificada', estadosValidos(), true) ? $_POST['estado'] : 'planificada',
        'planificador_id' => (int) ($_POST['planificador_id'] ?? 0),
        'duracion_estimada_horas' => (float) ($_POST['duracion_estimada_horas'] ?? 0),
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'observaciones_mantenedor' => trim($_POST['observaciones_mantenedor'] ?? ''),
        'observaciones_supervisor' => trim($_POST['observaciones_supervisor'] ?? ''),
        'motivo_suspension' => trim($_POST['motivo_suspension'] ?? ''),
    ];

    if ($formData['fecha_planificada'] === '' || $formData['equipo_id'] <= 0) {
        $error = 'La fecha planificada y el equipo son obligatorios.';
    }

    if ($formData['nivel_mantenimiento_id'] <= 0) {
        $error = $error ?: 'Seleccione un nivel de mantenimiento.';
    }

    if ($formData['planificador_id'] <= 0) {
        $error = $error ?: 'Seleccione un planificador responsable.';
    }

    if ($formData['hora_inicio'] !== '' && $formData['hora_fin'] !== '' && $formData['hora_inicio'] >= $formData['hora_fin']) {
        $error = $error ?: 'La hora de inicio debe ser anterior a la hora de fin.';
    }

    if (!$error) {
        if ($formData['id']) {
            $stmt = $pdo->prepare(
                'UPDATE ordenes_preventivas
                 SET equipo_id = ?, nivel_mantenimiento_id = ?, fecha_planificada = ?,
                     hora_inicio = ?, hora_fin = ?,
                     estado = ?, planificador_id = ?, duracion_estimada_horas = ?,
                     descripcion = ?, observaciones_mantenedor = ?,
                     observaciones_supervisor = ?,
                     motivo_suspension = ?, actualizada_en = NOW()
                 WHERE id = ?'
            );
            $stmt->execute([
                $formData['equipo_id'],
                $formData['nivel_mantenimiento_id'],
                $formData['fecha_planificada'],
                $formData['hora_inicio'] ?: null,
                $formData['hora_fin'] ?: null,
                $formData['estado'],
                $formData['planificador_id'],
                $formData['duracion_estimada_horas'],
                $formData['descripcion'] ?: null,
                $formData['observaciones_mantenedor'] ?: null,
                $formData['observaciones_supervisor'] ?: null,
                $formData['motivo_suspension'] ?: null,
                (int) $formData['id'],
            ]);
            $mensaje = 'Orden preventiva actualizada correctamente.';
        } else {
            $codigo = $formData['codigo_unico'] ?: generarCodigoUnico();
            $stmt = $pdo->prepare(
                'INSERT INTO ordenes_preventivas
                 (codigo_unico, equipo_id, nivel_mantenimiento_id, fecha_planificada,
                  hora_inicio, hora_fin, estado, planificador_id,
                  duracion_estimada_horas, descripcion, creada_en, actualizada_en)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
            );
            $stmt->execute([
                $codigo,
                $formData['equipo_id'],
                $formData['nivel_mantenimiento_id'],
                $formData['fecha_planificada'],
                $formData['hora_inicio'] ?: null,
                $formData['hora_fin'] ?: null,
                'planificada',
                $formData['planificador_id'],
                $formData['duracion_estimada_horas'] ?: 1,
                $formData['descripcion'] ?: null,
            ]);
            $mensaje = 'Orden preventiva creada correctamente. Código: ' . htmlspecialchars($codigo);
            $formData = [
                'id' => '',
                'codigo_unico' => '',
                'equipo_id' => '',
                'nivel_mantenimiento_id' => '',
                'fecha_planificada' => '',
                'hora_inicio' => '',
                'hora_fin' => '',
                'estado' => 'planificada',
                'planificador_id' => '',
                'duracion_estimada_horas' => '',
            'descripcion' => $orden['descripcion'] ?? '',
                'observaciones_mantenedor' => '',
                'observaciones_supervisor' => '',
                'motivo_suspension' => '',
            ];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_estado']) && $puedeCambiarEstado) {
    $otId = (int) ($_POST['cambiar_estado'] ?? 0);
    $nuevoEstado = $_POST['nuevo_estado'] ?? '';
    $codigoOtp = trim($_POST['codigo_otp'] ?? '');

    if ($otId > 0 && in_array($nuevoEstado, estadosValidos(), true)) {
        $stmt = $pdo->prepare('SELECT * FROM ordenes_preventivas WHERE id = ? LIMIT 1');
        $stmt->execute([$otId]);
        $orden = $stmt->fetch();

        if ($orden) {
            $transiciones = transicionesPermitidas($orden['estado']);
            if (!in_array($nuevoEstado, $transiciones, true)) {
                $error = 'No se puede cambiar de "' . $orden['estado'] . '" a "' . $nuevoEstado . '".';
            } elseif ($nuevoEstado === 'en_curso' && $codigoOtp === '') {
                $stmt = $pdo->prepare('SELECT codigo_otp_validacion FROM ordenes_preventivas WHERE id = ?');
                $stmt->execute([$otId]);
                $row = $stmt->fetch();
                $otpEsperado = $row['codigo_otp_validacion'] ?? '';

                if ($otpEsperado === '') {
                    $nuevoOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    $stmt = $pdo->prepare('UPDATE ordenes_preventivas SET codigo_otp_validacion = ? WHERE id = ?');
                    $stmt->execute([$nuevoOtp, $otId]);
                    $error = 'Se requiere código OTP para iniciar la orden. OTP generado: ' . $nuevoOtp;
                } else {
                    $error = 'Se requiere el código OTP para iniciar la orden. Consulte el código generado previamente.';
                }
            } elseif (in_array($nuevoEstado, ['en_curso', 'cerrada'], true) && $codigoOtp !== '') {
                $stmt = $pdo->prepare('SELECT codigo_otp_validacion FROM ordenes_preventivas WHERE id = ?');
                $stmt->execute([$otId]);
                $row = $stmt->fetch();
                $otpEsperado = $row['codigo_otp_validacion'] ?? '';

                if ($otpEsperado === '' || $otpEsperado !== $codigoOtp) {
                    $error = 'Código OTP inválido. Verifique el código e intente nuevamente.';
                } else {
                    $stmt = $pdo->prepare(
                        'UPDATE ordenes_preventivas
                         SET estado = ?, codigo_otp_validacion = NULL,
                             fecha_inicio_ejecucion = CASE WHEN ? = ? THEN NOW() ELSE fecha_inicio_ejecucion END,
                             fecha_cierre_ejecucion = CASE WHEN ? = ? THEN NOW() ELSE fecha_cierre_ejecucion END,
                             actualizada_en = NOW()
                         WHERE id = ?'
                    );
                    $stmt->execute([$nuevoEstado, $nuevoEstado, 'en_curso', $nuevoEstado, 'cerrada', $otId]);
                    $mensaje = 'Estado cambiado a "' . $nuevoEstado . '" correctamente.';
                }
            } else {
                $stmt = $pdo->prepare(
                    'UPDATE ordenes_preventivas
                     SET estado = ?, codigo_otp_validacion = NULL,
                         fecha_cierre_ejecucion = CASE WHEN ? = ? THEN NOW() ELSE fecha_cierre_ejecucion END,
                         fecha_inicio_ejecucion = CASE WHEN ? = ? THEN NOW() ELSE fecha_inicio_ejecucion END,
                         actualizada_en = NOW()
                     WHERE id = ?'
                );
                $stmt->execute([$nuevoEstado, $nuevoEstado, 'cerrada', $nuevoEstado, 'en_curso', $otId]);
                $mensaje = 'Estado cambiado a "' . $nuevoEstado . '" correctamente.';
            }
        }
    }
}

if (isset($_GET['edit']) && is_numeric($_GET['edit']) && $puedeEditar) {
    $otId = (int) $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM ordenes_preventivas WHERE id = ? LIMIT 1');
    $stmt->execute([$otId]);
    $orden = $stmt->fetch();
    if ($orden) {
        $formData = [
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
            'descripcion' => '',
            'observaciones_mantenedor' => $orden['observaciones_mantenedor'] ?? '',
            'observaciones_supervisor' => $orden['observaciones_supervisor'] ?? '',
            'motivo_suspension' => $orden['motivo_suspension'] ?? '',
        ];
    }
}

$filterEquipo = (int) ($_GET['filter_equipo'] ?? 0);
$filterEstado = in_array($_GET['filter_estado'] ?? '', array_merge(estadosValidos(), ['todos']), true) ? $_GET['filter_estado'] : 'todos';
$filterFechaDesde = trim($_GET['filter_fecha_desde'] ?? '');
$filterFechaHasta = trim($_GET['filter_fecha_hasta'] ?? '');

$where = ['1 = 1'];
$params = [];

if ($filterEquipo > 0) {
    $where[] = 'op.equipo_id = ?';
    $params[] = $filterEquipo;
}
if ($filterEstado !== 'todos') {
    $where[] = 'op.estado = ?';
    $params[] = $filterEstado;
}
if ($filterFechaDesde !== '') {
    $where[] = 'op.fecha_planificada >= ?';
    $params[] = $filterFechaDesde;
}
if ($filterFechaHasta !== '') {
    $where[] = 'op.fecha_planificada <= ?';
    $params[] = $filterFechaHasta;
}

$sql = sprintf(
    'SELECT op.*, e.nombre AS equipo_nombre, e.numero_activo_fijo,
            u.nombre_completo AS planificador_nombre
     FROM ordenes_preventivas op
     LEFT JOIN equipos e ON e.id = op.equipo_id
     LEFT JOIN usuarios u ON u.id = op.planificador_id
     WHERE %s
     ORDER BY op.fecha_planificada DESC, op.hora_inicio ASC',
    implode(' AND ', $where)
);
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$ordenes = $stmt->fetchAll();

$viewId = isset($_GET['view']) && is_numeric($_GET['view']) ? (int) $_GET['view'] : 0;
$viewDetalle = null;
if ($viewId > 0) {
    $stmt = $pdo->prepare(
        'SELECT op.*, e.nombre AS equipo_nombre, e.numero_activo_fijo,
                nm.nombre AS nivel_nombre, u.nombre_completo AS planificador_nombre,
                s.nombre_completo AS supervisor_nombre, m.nombre_completo AS mantenedor_nombre
         FROM ordenes_preventivas op
         LEFT JOIN equipos e ON e.id = op.equipo_id
         LEFT JOIN niveles_mantenimiento nm ON nm.id = op.nivel_mantenimiento_id
         LEFT JOIN usuarios u ON u.id = op.planificador_id
         LEFT JOIN usuarios s ON s.id = op.supervisor_asigno_id
         LEFT JOIN usuarios m ON m.id = op.mantenedor_id
         WHERE op.id = ? LIMIT 1'
    );
    $stmt->execute([$viewId]);
    $viewDetalle = $stmt->fetch();
}

$pageTitle = 'Órdenes Preventivas';
$pageSlug = 'preventivas';
require __DIR__ . '/includes/layout.php';
?>
                <div class="page-header">
                    <h1 class="page-title">Órdenes Preventivas</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
                <?php endif; ?>

<?php if ($viewDetalle): ?>
                <div class="page-card">
                    <div class="page-header" style="margin-bottom:1rem;">
                        <h2>Detalle: <?= htmlspecialchars($viewDetalle['codigo_unico']) ?></h2>
                        <a href="<?= BASE_PATH ?>/public/preventivas.php" class="btn btn-outline">&larr; Volver</a>
                    </div>
                    <div class="form-grid" style="grid-template-columns:1fr 1fr;">
                        <div><strong>Equipo:</strong> <?= htmlspecialchars($viewDetalle['equipo_nombre'] ?? '') ?> (<?= htmlspecialchars($viewDetalle['numero_activo_fijo'] ?? '') ?>)</div>
                        <div><strong>Nivel de mantenimiento:</strong> <?= htmlspecialchars($viewDetalle['nivel_nombre'] ?? 'N/A') ?></div>
                        <div><strong>Fecha planificada:</strong> <?= htmlspecialchars($viewDetalle['fecha_planificada']) ?></div>
                        <div><strong>Horario:</strong> <?= $viewDetalle['hora_inicio'] ? htmlspecialchars(substr($viewDetalle['hora_inicio'], 0, 5)) . ' - ' . htmlspecialchars(substr($viewDetalle['hora_fin'] ?? '', 0, 5)) : htmlspecialchars($viewDetalle['duracion_estimada_horas']) . ' h' ?></div>
                        <div><strong>Estado:</strong> <span class="tag tag-<?= htmlspecialchars($viewDetalle['estado']) ?>"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $viewDetalle['estado']))) ?></span></div>
                        <div><strong>Planificador:</strong> <?= htmlspecialchars($viewDetalle['planificador_nombre'] ?? 'N/A') ?></div>
                        <div><strong>Supervisor:</strong> <?= htmlspecialchars($viewDetalle['supervisor_nombre'] ?? 'Sin asignar') ?></div>
                        <div><strong>Mantenedor:</strong> <?= htmlspecialchars($viewDetalle['mantenedor_nombre'] ?? 'Sin asignar') ?></div>
                        <?php if ($viewDetalle['fecha_inicio_ejecucion']): ?>
                        <div><strong>Inicio ejecución:</strong> <?= htmlspecialchars($viewDetalle['fecha_inicio_ejecucion']) ?></div>
                        <?php endif; ?>
                        <?php if ($viewDetalle['fecha_cierre_ejecucion']): ?>
                        <div><strong>Cierre ejecución:</strong> <?= htmlspecialchars($viewDetalle['fecha_cierre_ejecucion']) ?></div>
                        <?php endif; ?>
                    </div>
                    <?php if ($viewDetalle['descripcion']): ?>
                    <div style="margin-top:1rem;">
                        <h3>Descripción</h3>
                        <p><?= nl2br(htmlspecialchars($viewDetalle['descripcion'])) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($viewDetalle['observaciones_mantenedor']): ?>
                    <div style="margin-top:0.5rem;">
                        <h3>Observaciones del mantenedor</h3>
                        <p><?= nl2br(htmlspecialchars($viewDetalle['observaciones_mantenedor'])) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($viewDetalle['observaciones_supervisor']): ?>
                    <div style="margin-top:0.5rem;">
                        <h3>Observaciones del supervisor</h3>
                        <p><?= nl2br(htmlspecialchars($viewDetalle['observaciones_supervisor'])) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if ($viewDetalle['motivo_suspension']): ?>
                    <div style="margin-top:0.5rem;">
                        <h3>Motivo de suspensión</h3>
                        <p><?= nl2br(htmlspecialchars($viewDetalle['motivo_suspension'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
<?php else: ?>
                <div class="page-card">
                    <form method="get" class="filter-panel" aria-label="Filtrar órdenes preventivas">
                        <div class="form-group">
                            <label for="filter_equipo">Equipo</label>
                            <select id="filter_equipo" name="filter_equipo">
                                <option value="0">Todos</option>
                                <?php foreach ($equipos as $eq): ?>
                                    <option value="<?= $eq['id'] ?>" <?= $filterEquipo === (int) $eq['id'] ? 'selected' : '' ?>><?= htmlspecialchars($eq['nombre'] . ' (' . $eq['numero_activo_fijo'] . ')') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_estado">Estado</label>
                            <select id="filter_estado" name="filter_estado">
                                <option value="todos" <?= $filterEstado === 'todos' ? 'selected' : '' ?>>Todos</option>
                                <option value="planificada" <?= $filterEstado === 'planificada' ? 'selected' : '' ?>>Planificada</option>
                                <option value="en_curso" <?= $filterEstado === 'en_curso' ? 'selected' : '' ?>>En curso</option>
                                <option value="cerrada" <?= $filterEstado === 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
                                <option value="suspendida" <?= $filterEstado === 'suspendida' ? 'selected' : '' ?>>Suspendida</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_fecha_desde">Fecha desde</label>
                            <input type="date" id="filter_fecha_desde" name="filter_fecha_desde" value="<?= htmlspecialchars($filterFechaDesde) ?>">
                        </div>
                        <div class="form-group">
                            <label for="filter_fecha_hasta">Fecha hasta</label>
                            <input type="date" id="filter_fecha_hasta" name="filter_fecha_hasta" value="<?= htmlspecialchars($filterFechaHasta) ?>">
                        </div>
                        <div class="form-group" style="align-self:end;">
                            <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                        </div>
                    </form>

                    <?php if ($puedeCrear): ?>
                        <p>
                            <a href="#formulario-preventiva" class="btn btn-primary">Crear nueva orden preventiva</a>
                        </p>
                    <?php endif; ?>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Equipo</th>
                                <th>Fecha planificada</th>
                                <th>Horario</th>
                                <th>Estado</th>
                                <th>Planificador</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ordenes)): ?>
                                <tr>
                                    <td colspan="7">No se encontraron órdenes preventivas con los filtros seleccionados.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($ordenes as $orden): ?>
                                <tr class="<?= $orden['estado'] === 'cerrada' ? 'inactive-row' : '' ?>">
                                    <td><strong><?= htmlspecialchars($orden['codigo_unico']) ?></strong></td>
                                    <td><?= htmlspecialchars($orden['equipo_nombre'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($orden['fecha_planificada']) ?></td>
                                    <td>
                                        <?php if ($orden['hora_inicio']): ?>
                                            <?= htmlspecialchars(substr($orden['hora_inicio'], 0, 5)) ?>
                                            - <?= htmlspecialchars(substr($orden['hora_fin'] ?? '', 0, 5)) ?>
                                        <?php else: ?>
                                            <?= htmlspecialchars($orden['duracion_estimada_horas']) ?> h
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="tag <?= 'tag-' . $orden['estado'] ?>">
                                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $orden['estado']))) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($orden['planificador_nombre'] ?? 'N/A') ?></td>
                                    <td>
                                        <a href="<?= BASE_PATH ?>/public/preventivas.php?view=<?= $orden['id'] ?>" class="btn btn-outline small-action">Ver</a>
                                        <?php if ($puedeEditar && $orden['estado'] === 'planificada'): ?>
                                            <a href="<?= BASE_PATH ?>/public/preventivas.php?edit=<?= $orden['id'] ?>" class="btn btn-outline small-action">Editar</a>
                                        <?php endif; ?>
                                        <?php if ($puedeCambiarEstado): ?>
                                            <?php $transiciones = transicionesPermitidas($orden['estado']); ?>
                                            <?php if (!empty($transiciones)): ?>
                                                <form method="post" style="display:inline-block;" class="form-inline">
                                                    <input type="hidden" name="cambiar_estado" value="<?= $orden['id'] ?>">
                                                    <select name="nuevo_estado" required style="font-size:0.85rem;padding:2px 4px;">
                                                        <option value="">Cambiar a...</option>
                                                        <?php foreach ($transiciones as $t): ?>
                                                            <option value="<?= $t ?>"><?= ucfirst(str_replace('_', ' ', $t)) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php if (in_array($orden['estado'], ['planificada', 'en_curso']) && in_array($orden['estado'] === 'planificada' ? 'en_curso' : 'cerrada', $transiciones, true)): ?>
                                                        <input type="text" name="codigo_otp" placeholder="OTP (si requiere)" style="font-size:0.85rem;padding:2px 4px;width:80px;">
                                                    <?php endif; ?>
                                                    <button type="submit" class="btn btn-outline small-action">→</button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="page-card" id="formulario-preventiva">
                    <h2><?= $formData['id'] ? 'Editar orden preventiva' : 'Crear orden preventiva' ?></h2>
                    <form method="post" class="form-grid">
                        <input type="hidden" name="save_preventiva" value="1">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($formData['id']) ?>">
                        <input type="hidden" name="codigo_unico" value="<?= htmlspecialchars($formData['codigo_unico'] ?: generarCodigoUnico()) ?>">

                        <div class="form-group">
                            <label for="equipo_id">Equipo</label>
                            <select id="equipo_id" name="equipo_id" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($equipos as $eq): ?>
                                    <option value="<?= $eq['id'] ?>" <?= $formData['equipo_id'] === (int) $eq['id'] ? 'selected' : '' ?>><?= htmlspecialchars($eq['nombre'] . ' (' . $eq['numero_activo_fijo'] . ')') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="nivel_mantenimiento_id">Nivel de mantenimiento</label>
                            <select id="nivel_mantenimiento_id" name="nivel_mantenimiento_id" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($nivelesMantenimiento as $nm): ?>
                                    <option value="<?= $nm['id'] ?>" <?= $formData['nivel_mantenimiento_id'] == $nm['id'] ? 'selected' : '' ?>><?= htmlspecialchars($nm['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="fecha_planificada">Fecha planificada</label>
                            <input type="date" id="fecha_planificada" name="fecha_planificada" value="<?= htmlspecialchars($formData['fecha_planificada']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="hora_inicio">Hora inicio</label>
                            <input type="time" id="hora_inicio" name="hora_inicio" value="<?= htmlspecialchars($formData['hora_inicio']) ?>">
                        </div>

                        <div class="form-group">
                            <label for="hora_fin">Hora fin</label>
                            <input type="time" id="hora_fin" name="hora_fin" value="<?= htmlspecialchars($formData['hora_fin']) ?>">
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="descripcion">Descripción de la orden</label>
                            <textarea id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($formData['descripcion']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="duracion_estimada_horas">Duración estimada (horas)</label>
                            <input type="number" id="duracion_estimada_horas" name="duracion_estimada_horas" step="0.5" min="0.5" value="<?= htmlspecialchars($formData['duracion_estimada_horas'] ?: '1') ?>">
                        </div>

                        <div class="form-group">
                            <label for="planificador_id">Planificador responsable</label>
                            <select id="planificador_id" name="planificador_id" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($planificadores as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= $formData['planificador_id'] === (int) $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['nombre_completo']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if ($formData['id']): ?>
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado" <?= esAdministrador() ? '' : 'disabled' ?>>
                                <option value="planificada" <?= $formData['estado'] === 'planificada' ? 'selected' : '' ?>>Planificada</option>
                                <option value="en_curso" <?= $formData['estado'] === 'en_curso' ? 'selected' : '' ?>>En curso</option>
                                <option value="cerrada" <?= $formData['estado'] === 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
                                <option value="suspendida" <?= $formData['estado'] === 'suspendida' ? 'selected' : '' ?>>Suspendida</option>
                            </select>
                            <?php if (!esAdministrador()): ?>
                                <input type="hidden" name="estado" value="<?= htmlspecialchars($formData['estado']) ?>">
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($formData['observaciones_mantenedor'] || $formData['observaciones_supervisor']): ?>
                        <div class="form-group">
                            <label for="observaciones_mantenedor">Observaciones del mantenedor</label>
                            <textarea id="observaciones_mantenedor" name="observaciones_mantenedor"><?= htmlspecialchars($formData['observaciones_mantenedor']) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="observaciones_supervisor">Observaciones del supervisor</label>
                            <textarea id="observaciones_supervisor" name="observaciones_supervisor"><?= htmlspecialchars($formData['observaciones_supervisor']) ?></textarea>
                        </div>
                        <?php endif; ?>

                        <?php if ($formData['estado'] === 'suspendida' || $formData['motivo_suspension']): ?>
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="motivo_suspension">Motivo de suspensión</label>
                            <textarea id="motivo_suspension" name="motivo_suspension"><?= htmlspecialchars($formData['motivo_suspension']) ?></textarea>
                        </div>
                        <?php endif; ?>

                        <div class="form-group" style="grid-column: 1 / -1; text-align:right;">
                            <button type="submit" class="btn btn-primary"><?= $formData['id'] ? 'Guardar cambios' : 'Crear orden preventiva' ?></button>
                        </div>
                    </form>
                </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/layout_footer.php'; ?>
