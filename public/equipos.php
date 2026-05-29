<?php
require_once __DIR__ . '/../auth/auth_functions.php';
requerirAutenticacion();

$allowedRoles = ['Supervisor', 'Planificador/Programador'];
$rolActual = $_SESSION['rol_nombre'] ?? '';
$accesoPermitido = esAdministrador() || in_array($rolActual, $allowedRoles, true) || tienePermiso('equipos', 'ver');
if (!$accesoPermitido) {
    header('Location: ' . BASE_PATH . '/public/index.php?error=sin_permiso');
    exit;
}

$pdo = getDbConnection();
$categorias = $pdo->query("SELECT id, nombre FROM categorias_equipo WHERE estado = 'activo' ORDER BY nombre")->fetchAll();
$zonas = $pdo->query("SELECT id, nombre FROM zonas WHERE estado = 'activo' ORDER BY nombre")->fetchAll();
$familias = $pdo->query("SELECT DISTINCT grupo_responsable FROM equipos WHERE grupo_responsable IS NOT NULL AND TRIM(grupo_responsable) <> '' ORDER BY grupo_responsable")->fetchAll(PDO::FETCH_COLUMN);

$puedeCrear = esAdministrador() || tienePermiso('equipos', 'crear');
$puedeEditar = esAdministrador() || tienePermiso('equipos', 'editar');
$puedeDesactivar = esAdministrador() || tienePermiso('equipos', 'desactivar');

$error = '';
$mensaje = '';
$formData = [
    'equipo_id' => '',
    'nombre' => '',
    'familia' => '',
    'categoria_id' => '',
    'zona_id' => '',
    'estado' => 'activo',
    'descripcion' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_equipo'])) {
    $formData = [
        'equipo_id' => $_POST['equipo_id'] ?? '',
        'nombre' => trim($_POST['nombre'] ?? ''),
        'familia' => trim($_POST['familia'] ?? ''),
        'categoria_id' => (int) ($_POST['categoria_id'] ?? 0),
        'zona_id' => (int) ($_POST['zona_id'] ?? 0),
        'estado' => in_array($_POST['estado'] ?? 'activo', ['activo', 'inactivo'], true) ? $_POST['estado'] : 'activo',
        'descripcion' => trim($_POST['descripcion'] ?? ''),
    ];

    if ($formData['nombre'] === '' || $formData['familia'] === '') {
        $error = 'El nombre y la familia son obligatorios.';
    }

    if (!$formData['categoria_id']) {
        $error = $error ?: 'Seleccione una categoría válida.';
    } else {
        $stmtValidar = $pdo->prepare('SELECT 1 FROM categorias_equipo WHERE id = ? AND estado = ?');
        $stmtValidar->execute([$formData['categoria_id'], 'activo']);
        if ($stmtValidar->rowCount() === 0) {
            $error = $error ?: 'Seleccione una categoría válida.';
        }
    }

    if (!$formData['zona_id']) {
        $error = $error ?: 'Seleccione una zona válida.';
    } else {
        $stmtValidar = $pdo->prepare('SELECT 1 FROM zonas WHERE id = ? AND estado = ?');
        $stmtValidar->execute([$formData['zona_id'], 'activo']);
        if ($stmtValidar->rowCount() === 0) {
            $error = $error ?: 'Seleccione una zona válida.';
        }
    }

    if ($formData['estado'] === 'inactivo' && !esAdministrador()) {
        $error = $error ?: 'Solo un Administrador puede desactivar un equipo.';
    }

    if (!$error) {
        if ($formData['equipo_id']) {
            $stmt = $pdo->prepare(
                'UPDATE equipos SET nombre = ?, grupo_responsable = ?, categoria_id = ?, zona_id = ?, estado = ?, descripcion = ?, modificado_por_usuario_id = ?, actualizado_en = NOW() WHERE id = ?'
            );
            $stmt->execute([
                $formData['nombre'],
                $formData['familia'],
                $formData['categoria_id'],
                $formData['zona_id'],
                $formData['estado'],
                $formData['descripcion'],
                $_SESSION['usuario_id'],
                (int) $formData['equipo_id'],
            ]);
            $mensaje = 'Equipo actualizado correctamente.';
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO equipos (nombre, grupo_responsable, categoria_id, zona_id, estado, descripcion, registrado_por_usuario_id, creado_en, actualizado_en)
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
            );
            $stmt->execute([
                $formData['nombre'],
                $formData['familia'],
                $formData['categoria_id'],
                $formData['zona_id'],
                $formData['estado'],
                $formData['descripcion'],
                $_SESSION['usuario_id'],
            ]);
            $mensaje = 'Equipo creado correctamente.';
            $formData = [
                'equipo_id' => '',
                'nombre' => '',
                'familia' => '',
                'categoria_id' => '',
                'zona_id' => '',
                'estado' => 'activo',
                'descripcion' => '',
            ];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_estado']) && $puedeDesactivar) {
    $equipoId = (int) ($_POST['toggle_estado'] ?? 0);
    if ($equipoId > 0) {
        $stmt = $pdo->prepare('SELECT estado FROM equipos WHERE id = ? LIMIT 1');
        $stmt->execute([$equipoId]);
        $equipo = $stmt->fetch();
        if ($equipo) {
            $nuevoEstado = $equipo['estado'] === 'activo' ? 'inactivo' : 'activo';
            $stmt = $pdo->prepare('UPDATE equipos SET estado = ?, actualizado_en = NOW() WHERE id = ?');
            $stmt->execute([$nuevoEstado, $equipoId]);
            $mensaje = $nuevoEstado === 'inactivo' ? 'Equipo desactivado.' : 'Equipo activado.';
        }
    }
}

if (isset($_GET['edit']) && is_numeric($_GET['edit']) && $puedeEditar) {
    $equipoId = (int) $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM equipos WHERE id = ? LIMIT 1');
    $stmt->execute([$equipoId]);
    $equipo = $stmt->fetch();
    if ($equipo) {
        $formData = [
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

$filterFamily = trim($_GET['filter_family'] ?? '');
$filterCategoria = (int) ($_GET['filter_categoria'] ?? 0);
$filterZona = (int) ($_GET['filter_zona'] ?? 0);
$filterEstado = in_array($_GET['filter_estado'] ?? '', ['activo', 'inactivo', 'todos'], true) ? $_GET['filter_estado'] : 'todos';

$where = ['1 = 1'];
$params = [];

if ($filterFamily !== '') {
    $where[] = 'e.grupo_responsable = ?';
    $params[] = $filterFamily;
}
if ($filterCategoria > 0) {
    $where[] = 'e.categoria_id = ?';
    $params[] = $filterCategoria;
}
if ($filterZona > 0) {
    $where[] = 'e.zona_id = ?';
    $params[] = $filterZona;
}
if ($filterEstado !== 'todos') {
    $where[] = 'e.estado = ?';
    $params[] = $filterEstado;
}

$query = sprintf(
    'SELECT e.id, e.nombre, e.grupo_responsable AS familia, c.nombre AS categoria, z.nombre AS zona, e.estado, e.descripcion
     FROM equipos e
     LEFT JOIN categorias_equipo c ON c.id = e.categoria_id
     LEFT JOIN zonas z ON z.id = e.zona_id
     WHERE %s
     ORDER BY e.nombre ASC',
    implode(' AND ', $where)
);
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$equipos = $stmt->fetchAll();

$pageTitle = 'Equipos';
$pageSlug = 'equipos';
require __DIR__ . '/includes/layout.php';
?>
                <div class="page-header">
                    <h1 class="page-title">Equipos</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($mensaje): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
                <?php endif; ?>

                <div class="page-card">
                    <form method="get" class="filter-panel" aria-label="Filtrar equipos">
                        <div class="form-group">
                            <label for="filter_family">Familia</label>
                            <select id="filter_family" name="filter_family">
                                <option value="">Todas</option>
                                <?php foreach ($familias as $familia): ?>
                                    <option value="<?= htmlspecialchars($familia) ?>" <?= $filterFamily === $familia ? 'selected' : '' ?>><?= htmlspecialchars($familia) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_categoria">Categoría</label>
                            <select id="filter_categoria" name="filter_categoria">
                                <option value="0">Todas</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>" <?= $filterCategoria === (int) $categoria['id'] ? 'selected' : '' ?>><?= htmlspecialchars($categoria['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_zona">Zona</label>
                            <select id="filter_zona" name="filter_zona">
                                <option value="0">Todas</option>
                                <?php foreach ($zonas as $zona): ?>
                                    <option value="<?= $zona['id'] ?>" <?= $filterZona === (int) $zona['id'] ? 'selected' : '' ?>><?= htmlspecialchars($zona['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter_estado">Estado</label>
                            <select id="filter_estado" name="filter_estado">
                                <option value="todos" <?= $filterEstado === 'todos' ? 'selected' : '' ?>>Todos</option>
                                <option value="activo" <?= $filterEstado === 'activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= $filterEstado === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>
                        <div class="form-group" style="align-self:end;">
                            <button type="submit" class="btn btn-primary">Aplicar filtros</button>
                        </div>
                    </form>

                    <?php if ($puedeCrear): ?>
                        <p>
                            <a href="#formulario-equipo" class="btn btn-primary">Crear nuevo equipo</a>
                        </p>
                    <?php endif; ?>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Familia</th>
                                <th>Categoría</th>
                                <th>Zona</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($equipos)): ?>
                                <tr>
                                    <td colspan="6">No se encontraron equipos con los filtros seleccionados.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($equipos as $equipo): ?>
                                <tr class="<?= $equipo['estado'] === 'inactivo' ? 'inactive-row' : '' ?>">
                                    <td><?= htmlspecialchars($equipo['nombre']) ?></td>
                                    <td><?= htmlspecialchars($equipo['familia'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($equipo['categoria'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($equipo['zona'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="tag <?= $equipo['estado'] === 'activo' ? 'tag-active' : 'tag-inactive' ?>">
                                            <?= htmlspecialchars(ucfirst($equipo['estado'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($puedeEditar): ?>
                                            <a href="<?= BASE_PATH ?>/public/equipos.php?edit=<?= $equipo['id'] ?>" class="btn btn-outline small-action">Editar</a>
                                        <?php endif; ?>
                                        <?php if ($puedeDesactivar): ?>
                                            <form method="post" style="display:inline-block;" onsubmit="return confirm('¿Desea cambiar el estado de este equipo?');">
                                                <input type="hidden" name="toggle_estado" value="<?= $equipo['id'] ?>">
                                                <button type="submit" class="btn btn-outline">
                                                    <?= $equipo['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="page-card" id="formulario-equipo">
                    <h2><?= $formData['equipo_id'] ? 'Editar equipo' : 'Crear equipo' ?></h2>
                    <form method="post" class="form-grid">
                        <input type="hidden" name="save_equipo" value="1">
                        <input type="hidden" name="equipo_id" value="<?= htmlspecialchars($formData['equipo_id']) ?>">

                        <div class="form-group">
                            <label for="nombre">Nombre del equipo</label>
                            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($formData['nombre']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="familia">Familia</label>
                            <input type="text" id="familia" name="familia" value="<?= htmlspecialchars($formData['familia']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="categoria_id">Categoría</label>
                            <select id="categoria_id" name="categoria_id" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria['id'] ?>" <?= $formData['categoria_id'] === (int) $categoria['id'] ? 'selected' : '' ?>><?= htmlspecialchars($categoria['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="zona_id">Zona</label>
                            <select id="zona_id" name="zona_id" required>
                                <option value="">Seleccione</option>
                                <?php foreach ($zonas as $zona): ?>
                                    <option value="<?= $zona['id'] ?>" <?= $formData['zona_id'] === (int) $zona['id'] ? 'selected' : '' ?>><?= htmlspecialchars($zona['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select id="estado" name="estado" <?= esAdministrador() ? '' : 'disabled' ?>>
                                <option value="activo" <?= $formData['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                                <option value="inactivo" <?= $formData['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                            <?php if (!esAdministrador()): ?>
                                <input type="hidden" name="estado" value="<?= htmlspecialchars($formData['estado']) ?>">
                            <?php endif; ?>
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="descripcion">Descripción</label>
                            <textarea id="descripcion" name="descripcion"><?= htmlspecialchars($formData['descripcion']) ?></textarea>
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1; text-align:right;">
                            <button type="submit" class="btn btn-primary"><?= $formData['equipo_id'] ? 'Guardar cambios' : 'Crear equipo' ?></button>
                        </div>
                    </form>
                </div>
<?php require __DIR__ . '/includes/layout_footer.php'; ?>
