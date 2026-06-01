<div class="page-header">
    <h1 class="page-title">Resumen mensual</h1>
    <a href="<?= \App\Core\App::BASE_PATH ?>/reportes" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="page-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Mes</th>
                <th>Total preventivas</th>
                <th>Completadas</th>
                <th>En curso</th>
                <th>Suspendidas</th>
                <th>% Cumplimiento</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($resumen)): ?>
                <tr><td colspan="6">No hay datos disponibles.</td></tr>
            <?php else: ?>
                <?php foreach ($resumen as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['mes']) ?></td>
                    <td><?= $row['preventivas_total'] ?></td>
                    <td><?= $row['preventivas_completadas'] ?></td>
                    <td><?= $row['preventivas_en_curso'] ?></td>
                    <td><?= $row['preventivas_suspendidas'] ?></td>
                    <td>
                        <?php
                        $pct = $row['preventivas_total'] > 0
                            ? round(($row['preventivas_completadas'] / $row['preventivas_total']) * 100, 1)
                            : 0;
                        ?>
                        <span class="badge badge-<?= $pct >= 80 ? 'success' : ($pct >= 50 ? 'warning' : 'danger') ?>">
                            <?= $pct ?>%
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
