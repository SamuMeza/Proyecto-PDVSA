<div class="page-header">
    <h1 class="page-title">Cumplimiento preventivo</h1>
    <a href="<?= \App\Core\App::BASE_PATH ?>/reportes" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="card-grid">
    <div class="card">
        <h3>Preventivas cerradas</h3>
        <p class="stat"><?= $stats['cerradas'] ?? 0 ?></p>
    </div>
    <div class="card">
        <h3>Preventivas abiertas</h3>
        <p class="stat"><?= $stats['abiertas'] ?? 0 ?></p>
    </div>
    <div class="card">
        <h3>Correctivas totales</h3>
        <p class="stat"><?= $stats['total'] ?? 0 ?></p>
    </div>
</div>

<div class="page-card" style="margin-bottom:1rem;">
    <h2 style="margin-bottom:1rem;">Cumplimiento por mes</h2>
    <div id="chart-cumplimiento" data-chart='<?= json_encode($chartData) ?>'></div>
</div>

<div class="page-card">
    <h2 style="margin-bottom:1rem;">Detalle mensual</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th>Mes</th>
                <th>Total</th>
                <th>Completadas</th>
                <th>Suspendidas</th>
                <th>Pendientes</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data['por_mes'])): ?>
                <tr><td colspan="5">No hay datos disponibles.</td></tr>
            <?php else: ?>
                <?php foreach ($data['por_mes'] as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['mes']) ?></td>
                    <td><?= $row['total'] ?></td>
                    <td><?= $row['completadas'] ?></td>
                    <td><?= $row['suspendidas'] ?></td>
                    <td><?= $row['pendientes'] ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
