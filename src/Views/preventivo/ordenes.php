<h2>Órdenes Preventivas</h2>
<table class="table">
    <thead>
        <tr>
            <th>Código</th>
            <th>Equipo</th>
            <th>Fecha Planificada</th>
            <th>Estado</th>
            <th>Mantenedor</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ordenes as $o): ?>
        <tr>
            <td><?= htmlspecialchars($o['codigo_unico']) ?></td>
            <td><?= htmlspecialchars($o['equipo_nombre'] ?? '') ?></td>
            <td><?= htmlspecialchars($o['fecha_planificada']) ?></td>
            <td><?= htmlspecialchars($o['estado']) ?></td>
            <td><?= htmlspecialchars($o['mantenedor_nombre'] ?? '-') ?></td>
            <td>
                <a href="<?= \App\Core\App::BASE_PATH ?>/public/preventivas.php?action=show&id=<?= $o['id'] ?>">Ver</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
