<h2>Ficha del Equipo</h2>
<div class="card">
    <table class="table-detail">
        <tr><th>Activo Fijo</th><td><?= htmlspecialchars($equipo['numero_activo_fijo']) ?></td></tr>
        <tr><th>Nombre</th><td><?= htmlspecialchars($equipo['nombre']) ?></td></tr>
        <tr><th>Marca</th><td><?= htmlspecialchars($equipo['marca'] ?? '-') ?></td></tr>
        <tr><th>Modelo</th><td><?= htmlspecialchars($equipo['modelo'] ?? '-') ?></td></tr>
        <tr><th>Serial</th><td><?= htmlspecialchars($equipo['serial'] ?? '-') ?></td></tr>
        <tr><th>Categoría</th><td><?= htmlspecialchars($equipo['categoria_nombre'] ?? '-') ?></td></tr>
        <tr><th>Zona</th><td><?= htmlspecialchars($equipo['zona_nombre'] ?? '-') ?></td></tr>
        <tr><th>Estado</th><td><?= htmlspecialchars($equipo['estado']) ?></td></tr>
    </table>
</div>
<div class="actions">
    <a href="<?= \App\Core\App::BASE_PATH ?>/public/equipos.php?action=edit&id=<?= $equipo['id'] ?>" class="btn btn-primary">Editar</a>
    <a href="<?= \App\Core\App::BASE_PATH ?>/public/equipos.php" class="btn btn-secondary">Volver</a>
</div>
