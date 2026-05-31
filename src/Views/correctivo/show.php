<h2>Orden Correctiva: <?= htmlspecialchars($orden['codigo_unico']) ?></h2>
<div class="card">
    <table class="table-detail">
        <tr><th>Equipo</th><td><?= htmlspecialchars($orden['equipo_nombre'] ?? '-') ?></td></tr>
        <tr><th>Tipo de Falla</th><td><?= htmlspecialchars($orden['tipo_falla_nombre'] ?? '-') ?></td></tr>
        <tr><th>Prioridad</th><td><?= htmlspecialchars($orden['prioridad_nombre'] ?? '-') ?></td></tr>
        <tr><th>Fecha Reporte</th><td><?= htmlspecialchars($orden['fecha_reporte']) ?></td></tr>
        <tr><th>Estado</th><td><?= htmlspecialchars($orden['estado']) ?></td></tr>
        <tr><th>Descripción</th><td><?= nl2br(htmlspecialchars($orden['descripcion_falla'])) ?></td></tr>
        <?php if ($orden['causa_raiz']): ?>
        <tr><th>Causa Raíz</th><td><?= nl2br(htmlspecialchars($orden['causa_raiz'])) ?></td></tr>
        <?php endif; ?>
    </table>
</div>
<a href="<?= \App\Core\App::BASE_PATH ?>/public/correctivas.php" class="btn btn-secondary">Volver</a>
