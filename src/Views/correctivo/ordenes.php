<h2>Órdenes Correctivas</h2>
<table class="table">
    <thead>
        <tr>
            <th>Código</th>
            <th>Equipo</th>
            <th>Fecha Reporte</th>
            <th>Tipo Falla</th>
            <th>Prioridad</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($ordenes as $o): ?>
        <tr>
            <td><?= htmlspecialchars($o['codigo_unico']) ?></td>
            <td><?= htmlspecialchars($o['equipo_nombre'] ?? '') ?></td>
            <td><?= htmlspecialchars($o['fecha_reporte']) ?></td>
            <td><?= htmlspecialchars($o['tipo_falla_nombre'] ?? '') ?></td>
            <td><?= htmlspecialchars($o['prioridad_nombre'] ?? '') ?></td>
            <td><?= htmlspecialchars($o['estado']) ?></td>
            <td><a href="?action=show&id=<?= $o['id'] ?>">Ver</a></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
