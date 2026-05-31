<?php
require_once __DIR__ . '/../config/autoload.php';

use App\Core\App;
use App\Core\Session;
use App\Core\Database;
use App\Services\AuthService;
use App\Models\ConfiguracionSistema;
use App\Models\CategoriaEquipo;
use App\Models\OrdenPreventiva;

AuthService::requireAuth();

$pdo = Database::connection();

$rolActual = Session::get('rol_nombre', '');
$puedeCrear = AuthService::isAdmin();
$puedeEditar = AuthService::isAdmin() || in_array($rolActual, ['Supervisor', 'Planificador/Programador'], true);
$puedeEliminar = AuthService::isAdmin();

$view = $_GET['view'] ?? 'week';
$viewMode = in_array($view, ['week', 'month'], true) ? $view : 'week';
$familiaFilter = trim($_GET['familia'] ?? '');
$dayDetail = (int) ($_GET['day'] ?? 0);

$familias = CategoriaEquipo::raw(
    "SELECT DISTINCT c.nombre, c.color_calendario FROM categorias_equipo c WHERE c.estado = 'activo' ORDER BY c.nombre"
);

$coloresConfigJson = ConfiguracionSistema::get('colores_familia_calendario', '{}');
$coloresConfig = json_decode($coloresConfigJson, true) ?: [];

if ($viewMode === 'week') {
    $weekStart = $_GET['week_start'] ?? date('Y-m-d', strtotime('monday this week'));
    $weekStartDate = new DateTime($weekStart);
    $weekEndDate = (clone $weekStartDate)->modify('+6 days');
    $dias = [];
    for ($i = 0; $i < 7; $i++) {
        $dias[] = (clone $weekStartDate)->modify("+$i days")->format('Y-m-d');
    }

    $where = ['op.fecha_planificada BETWEEN ? AND ?', "op.estado NOT IN ('cerrada','suspendida')"];
    $params = [$dias[0], $dias[6]];

    if ($familiaFilter !== '') {
        $where[] = 'c.nombre = ?';
        $params[] = $familiaFilter;
    }

    $ordenes = OrdenPreventiva::raw(
        'SELECT op.id, op.codigo_unico, op.fecha_planificada, op.hora_inicio, op.hora_fin,
                op.estado, e.nombre AS equipo_nombre, c.nombre AS familia, c.color_calendario
         FROM ordenes_preventivas op
         JOIN equipos e ON e.id = op.equipo_id
         JOIN categorias_equipo c ON c.id = e.categoria_id
         WHERE ' . implode(' AND ', $where) . '
         ORDER BY op.fecha_planificada ASC, op.hora_inicio ASC',
        $params
    );

    $eventosPorDia = [];
    foreach ($ordenes as $o) {
        $diaIdx = array_search($o['fecha_planificada'], $dias, true);
        if ($diaIdx === false) continue;
        $horaInicio = $o['hora_inicio'] ? (int) substr($o['hora_inicio'], 0, 2) : 0;
        $horaFin = $o['hora_fin'] ? (int) substr($o['hora_fin'], 0, 2) : $horaInicio + 1;
        if ($horaFin <= $horaInicio) $horaFin = $horaInicio + 1;
        $color = $coloresConfig[$o['familia']] ?? $o['color_calendario'] ?? '#7BA7D9';
        for ($h = $horaInicio; $h < $horaFin && $h < 24; $h++) {
            $eventosPorDia[$diaIdx][$h][] = [
                'id' => $o['id'],
                'codigo' => $o['codigo_unico'],
                'equipo' => $o['equipo_nombre'],
                'estado' => $o['estado'],
                'color' => $color,
                'familia' => $o['familia'],
                'inicio' => $o['hora_inicio'],
                'fin' => $o['hora_fin'],
            ];
        }
    }
}

if ($viewMode === 'month') {
    $month = $_GET['month'] ?? date('Y-m');
    $monthDate = new DateTime($month . '-01');
    $year = (int) $monthDate->format('Y');
    $mesNum = (int) $monthDate->format('m');
    $daysInMonth = (int) $monthDate->format('t');
    $firstDayOfWeek = (int) $monthDate->format('N');
    $prevMonth = (clone $monthDate)->modify('-1 month');
    $nextMonth = (clone $monthDate)->modify('+1 month');

    $where = [
        'op.fecha_planificada BETWEEN ? AND ?',
        "op.estado NOT IN ('cerrada','suspendida')",
    ];
    $params = [$month . '-01', $month . '-' . $daysInMonth];

    if ($familiaFilter !== '') {
        $where[] = 'c.nombre = ?';
        $params[] = $familiaFilter;
    }

    $ordenes = OrdenPreventiva::raw(
        'SELECT op.id, op.codigo_unico, op.fecha_planificada, op.hora_inicio, op.hora_fin,
                op.estado, e.nombre AS equipo_nombre, c.nombre AS familia, c.color_calendario,
                nm.nombre_nivel AS nivel
         FROM ordenes_preventivas op
         JOIN equipos e ON e.id = op.equipo_id
         JOIN categorias_equipo c ON c.id = e.categoria_id
         LEFT JOIN niveles_mantenimiento nm ON nm.id = op.nivel_mantenimiento_id
         WHERE ' . implode(' AND ', $where) . '
         ORDER BY op.fecha_planificada ASC, op.hora_inicio ASC',
        $params
    );

    $eventosPorDia = [];
    $ordenesPorDia = [];
    foreach ($ordenes as $o) {
        $dia = (int) substr($o['fecha_planificada'], 8, 2);
        $eventosPorDia[$dia][] = $o;
        $ordenesPorDia[$dia] = ($ordenesPorDia[$dia] ?? 0) + 1;
    }

    $selectedDay = null;
    $selectedDayOrders = [];
    if ($dayDetail > 0 && $dayDetail <= $daysInMonth && isset($eventosPorDia[$dayDetail])) {
        $selectedDay = $dayDetail;
        $selectedDayOrders = $eventosPorDia[$dayDetail];
    }
}

$pageTitle = 'Calendario de mantenimiento';
$pageSlug = 'calendario';
require __DIR__ . '/includes/layout.php';
?>
                <div class="page-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.5rem;">
                    <h1 class="page-title">Calendario de mantenimiento</h1>
                    <div style="display:flex; gap:0.5rem; align-items:center;">
                        <a href="?view=week<?= $familiaFilter ? '&familia=' . urlencode($familiaFilter) : '' ?>" class="btn <?= $viewMode === 'week' ? 'btn-primary' : 'btn-outline' ?>">Semanal</a>
                        <a href="?view=month<?= $familiaFilter ? '&familia=' . urlencode($familiaFilter) : '' ?>" class="btn <?= $viewMode === 'month' ? 'btn-primary' : 'btn-outline' ?>">Mensual</a>
                    </div>
                </div>

                <div class="page-card" style="margin-bottom:1rem;">
                    <form method="get" class="filter-panel" aria-label="Filtros del calendario">
                        <input type="hidden" name="view" value="<?= htmlspecialchars($viewMode) ?>">
                        <?php if ($viewMode === 'week' && isset($weekStart)): ?>
                            <input type="hidden" name="week_start" value="<?= htmlspecialchars($weekStart) ?>">
                        <?php endif; ?>
                        <?php if ($viewMode === 'month' && isset($month)): ?>
                            <input type="hidden" name="month" value="<?= htmlspecialchars($month) ?>">
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="familia">Familia de equipos</label>
                            <select id="familia" name="familia">
                                <option value="">Todas las familias</option>
                                <?php foreach ($familias as $f): ?>
                                    <option value="<?= htmlspecialchars($f['nombre']) ?>" <?= $familiaFilter === $f['nombre'] ? 'selected' : '' ?>><?= htmlspecialchars($f['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" style="align-self:end;">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                    </form>
                </div>

<?php if ($viewMode === 'week' && isset($weekStart)): ?>
                <div class="page-card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                        <a href="?view=week&week_start=<?= (clone $weekStartDate)->modify('-7 days')->format('Y-m-d') ?>&familia=<?= urlencode($familiaFilter) ?>" class="btn btn-outline">&larr; Semana anterior</a>
                        <strong>Semana del <?= htmlspecialchars($dias[0]) ?> al <?= htmlspecialchars($dias[6]) ?></strong>
                        <a href="?view=week&week_start=<?= (clone $weekStartDate)->modify('+7 days')->format('Y-m-d') ?>&familia=<?= urlencode($familiaFilter) ?>" class="btn btn-outline">Siguiente semana &rarr;</a>
                    </div>

                    <div class="cal-week-grid">
                        <div class="cal-week-header">
                            <div class="cal-hour-label">Hora</div>
                            <?php $nombresDias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo']; ?>
                            <?php foreach ($dias as $i => $dia): ?>
                                <div class="cal-day-header"><?= $nombresDias[$i] ?><br><small><?= htmlspecialchars($dia) ?></small></div>
                            <?php endforeach; ?>
                        </div>
                        <?php for ($h = 6; $h <= 20; $h++): ?>
                            <div class="cal-week-row">
                                <div class="cal-hour-label"><?= sprintf('%02d:00', $h) ?></div>
                                <?php for ($d = 0; $d < 7; $d++): ?>
                                    <div class="cal-cell">
                                        <?php if (isset($eventosPorDia[$d][$h])): ?>
                                            <?php foreach ($eventosPorDia[$d][$h] as $ev): ?>
                                                <div class="cal-event" style="background:<?= htmlspecialchars($ev['color']) ?>20; border-left:3px solid <?= htmlspecialchars($ev['color']) ?>; padding:2px 4px; margin:1px 0; font-size:0.75rem; border-radius:2px;">
                                                    <a href="<?= App::BASE_PATH ?>/public/preventivas.php?view=<?= $ev['id'] ?>" style="color:inherit;text-decoration:none;">
                                                        <strong><?= htmlspecialchars($ev['codigo']) ?></strong>
                                                        <?= htmlspecialchars($ev['equipo']) ?>
                                                        <?php if ($ev['inicio']): ?><br><small><?= htmlspecialchars(substr($ev['inicio'], 0, 5)) ?>-<?= htmlspecialchars(substr($ev['fin'], 0, 5)) ?></small><?php endif; ?>
                                                    </a>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        <?php endfor; ?>
                        <?php if (empty($ordenes)): ?>
                            <p style="text-align:center; padding:1rem; color:var(--text-muted);">No hay órdenes preventivas programadas para esta semana.</p>
                        <?php endif; ?>
                    </div>
                </div>

<?php elseif ($viewMode === 'month' && isset($month)): ?>
                <div class="page-card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                        <a href="?view=month&month=<?= $prevMonth->format('Y-m') ?>&familia=<?= urlencode($familiaFilter) ?>" class="btn btn-outline">&larr; Mes anterior</a>
                        <strong><?= strtoupper($monthDate->format('F Y')) ?></strong>
                        <a href="?view=month&month=<?= $nextMonth->format('Y-m') ?>&familia=<?= urlencode($familiaFilter) ?>" class="btn btn-outline">Siguiente mes &rarr;</a>
                    </div>

                    <div class="cal-month-grid">
                        <div class="cal-month-header">
                            <div>Lun</div><div>Mar</div><div>Mié</div><div>Jue</div><div>Vie</div><div>Sáb</div><div>Dom</div>
                        </div>
                        <?php
                        $totalCells = $firstDayOfWeek - 1 + $daysInMonth;
                        $totalRows = (int) ceil($totalCells / 7);
                        $dayCounter = 1;
                        ?>
                        <?php for ($row = 0; $row < $totalRows; $row++): ?>
                            <div class="cal-month-row">
                                <?php for ($col = 0; $col < 7; $col++): ?>
                                    <?php
                                    $cellIdx = $row * 7 + $col;
                                    $isValid = $cellIdx >= $firstDayOfWeek - 1 && $dayCounter <= $daysInMonth;
                                    ?>
                                    <div class="cal-month-cell <?= $isValid ? '' : 'cal-empty' ?> <?= $isValid && $dayCounter === (int) date('j') && $month === date('Y-m') ? 'cal-today' : '' ?> <?= $isValid && $dayCounter === $selectedDay ? 'cal-selected' : '' ?>">
                                        <?php if ($isValid): ?>
                                            <div class="cal-day-number">
                                                <?php if (isset($ordenesPorDia[$dayCounter])): ?>
                                                    <a href="?view=month&month=<?= htmlspecialchars($month) ?>&day=<?= $dayCounter ?>&familia=<?= urlencode($familiaFilter) ?>" style="text-decoration:none;color:inherit;"><?= $dayCounter ?></a>
                                                <?php else: ?>
                                                    <?= $dayCounter ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="cal-day-summary">
                                                <?php if (isset($ordenesPorDia[$dayCounter])): ?>
                                                    <span class="cal-day-badge"><?= $ordenesPorDia[$dayCounter] ?> OT</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (isset($eventosPorDia[$dayCounter])): ?>
                                                <?php foreach (array_slice($eventosPorDia[$dayCounter], 0, 2) as $ev): ?>
                                                    <?php $evColor = $coloresConfig[$ev['familia']] ?? $ev['color_calendario'] ?? '#7BA7D9'; ?>
                                                    <div class="cal-month-event" style="background:<?= htmlspecialchars($evColor) ?>40; border-left:2px solid <?= htmlspecialchars($evColor) ?>;">
                                                        <?= htmlspecialchars($ev['codigo_unico']) ?>
                                                    </div>
                                                <?php endforeach; ?>
                                                <?php if (count($eventosPorDia[$dayCounter]) > 2): ?>
                                                    <div class="cal-month-more">+<?= count($eventosPorDia[$dayCounter]) - 2 ?> más</div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php $dayCounter++; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <?php if ($selectedDay !== null): ?>
                <div class="page-card">
                    <h3>Órdenes del día <?= htmlspecialchars(sprintf('%02d', $selectedDay)) ?>/<?= htmlspecialchars(sprintf('%02d', $mesNum)) ?>/<?= htmlspecialchars($year) ?></h3>
                    <table class="data-table">
                        <thead><tr>
                            <th>Código</th><th>Equipo</th><th>Familia</th><th>Horario</th><th>Nivel</th><th>Estado</th><th>Acción</th>
                        </tr></thead>
                        <tbody>
                            <?php foreach ($selectedDayOrders as $ev): ?>
                                <?php $evColor = $coloresConfig[$ev['familia']] ?? $ev['color_calendario'] ?? '#7BA7D9'; ?>
                                <tr style="border-left:4px solid <?= htmlspecialchars($evColor) ?>;">
                                    <td><strong><?= htmlspecialchars($ev['codigo_unico']) ?></strong></td>
                                    <td><?= htmlspecialchars($ev['equipo_nombre']) ?></td>
                                    <td><span style="color:<?= htmlspecialchars($evColor) ?>;"><?= htmlspecialchars($ev['familia']) ?></span></td>
                                    <td><?= $ev['hora_inicio'] ? htmlspecialchars(substr($ev['hora_inicio'], 0, 5)) . ' - ' . htmlspecialchars(substr($ev['hora_fin'], 0, 5)) : '—' ?></td>
                                    <td><?= htmlspecialchars($ev['nivel'] ?? '—') ?></td>
                                    <td><span class="tag tag-<?= htmlspecialchars($ev['estado']) ?>"><?= htmlspecialchars(ucfirst($ev['estado'])) ?></span></td>
                                    <td><a href="<?= App::BASE_PATH ?>/public/preventivas.php?view=<?= $ev['id'] ?>" class="btn btn-outline small-action">Ver</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/includes/layout_footer.php'; ?>
