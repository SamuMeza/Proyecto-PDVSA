<div class="page-header">
    <h1 class="page-title">Estadisticas de fallas</h1>
    <a href="<?= \App\Core\App::BASE_PATH ?>/reportes" class="btn btn-outline">&larr; Volver</a>
</div>

<div class="card-grid">
    <div class="page-card">
        <h2 style="margin-bottom:1rem;">Por tipo de falla</h2>
        <div id="chart-fallas-tipo" data-chart='<?= json_encode($chartPorTipo) ?>'></div>
        <table class="data-table" style="margin-top:1rem;">
            <thead>
                <tr><th>Tipo de falla</th><th>Cantidad</th></tr>
            </thead>
            <tbody>
                <?php if (empty($fallas['por_tipo'])): ?>
                    <tr><td colspan="2">No hay datos.</td></tr>
                <?php else: ?>
                    <?php foreach ($fallas['por_tipo'] as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['tipo_falla'] ?? 'Sin clasificar') ?></td>
                        <td><?= $row['cantidad'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="page-card">
        <h2 style="margin-bottom:1rem;">Por zona</h2>
        <div id="chart-fallas-zona" data-chart='<?= json_encode($chartPorZona) ?>'></div>
        <table class="data-table" style="margin-top:1rem;">
            <thead>
                <tr><th>Zona</th><th>Cantidad</th></tr>
            </thead>
            <tbody>
                <?php if (empty($fallas['por_zona'])): ?>
                    <tr><td colspan="2">No hay datos.</td></tr>
                <?php else: ?>
                    <?php foreach ($fallas['por_zona'] as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['zona'] ?? 'Sin zona') ?></td>
                        <td><?= $row['cantidad'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="page-card">
    <h2 style="margin-bottom:1rem;">Fallas por mes</h2>
    <table class="data-table">
        <thead>
            <tr><th>Mes</th><th>Cantidad</th></tr>
        </thead>
        <tbody>
            <?php if (empty($fallas['por_mes'])): ?>
                <tr><td colspan="2">No hay datos.</td></tr>
            <?php else: ?>
                <?php foreach ($fallas['por_mes'] as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['mes']) ?></td>
                    <td><?= $row['cantidad'] ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
