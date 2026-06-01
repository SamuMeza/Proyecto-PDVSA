<div class="page-header">
    <h1 class="page-title">Rendimiento por tecnico</h1>
    <a href="<?= \App\Core\App::BASE_PATH ?>/reportes" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="page-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Tecnico</th>
                <th>Asignadas</th>
                <th>Completadas</th>
                <th>Pendientes</th>
                <th>Cumplimiento</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tecnicos)): ?>
                <tr><td colspan="5">No hay datos disponibles.</td></tr>
            <?php else: ?>
                <?php foreach ($tecnicos as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t['tecnico']) ?></td>
                    <td><?= $t['total_asignadas'] ?></td>
                    <td><?= $t['completadas'] ?></td>
                    <td><?= $t['pendientes'] ?></td>
                    <td>
                        <span class="badge badge-<?= $t['cumplimiento_pct'] >= 80 ? 'success' : ($t['cumplimiento_pct'] >= 50 ? 'warning' : 'danger') ?>">
                            <?= $t['cumplimiento_pct'] ?>%
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
