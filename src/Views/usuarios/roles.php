<h2>Roles y Permisos</h2>
<table class="table">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($roles as $r): ?>
        <tr>
            <td><?= htmlspecialchars($r['nombre']) ?></td>
            <td><?= htmlspecialchars($r['descripcion'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['estado']) ?></td>
            <td>-</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
