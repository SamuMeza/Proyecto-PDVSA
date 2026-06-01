<h2>Orden Preventiva: <?= htmlspecialchars($orden['codigo_unico']) ?></h2>
<div class="card">
    <table class="table-detail">
        <tr><th>Equipo</th><td><?= htmlspecialchars($orden['equipo_nombre'] ?? '-') ?></td></tr>
        <tr><th>Fecha Planificada</th><td><?= htmlspecialchars($orden['fecha_planificada']) ?></td></tr>
        <tr><th>Estado</th><td><?= htmlspecialchars($orden['estado']) ?></td></tr>
        <tr><th>Planificador</th><td><?= htmlspecialchars($orden['planificador_nombre'] ?? '-') ?></td></tr>
        <tr><th>Mantenedor</th><td><?= htmlspecialchars($orden['mantenedor_nombre'] ?? '-') ?></td></tr>
    </table>
</div>
<a href="<?= \App\Core\App::BASE_PATH ?>/preventivas" class="btn btn-secondary">Volver</a>
