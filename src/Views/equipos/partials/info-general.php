<div class="tab-pane active" id="info-general">
    <table class="table-detail">
        <tr><th>Número Activo Fijo</th><td><?= htmlspecialchars($equipo['numero_activo_fijo']) ?></td></tr>
        <tr><th>Nombre</th><td><?= htmlspecialchars($equipo['nombre']) ?></td></tr>
        <tr><th>Marca</th><td><?= htmlspecialchars($equipo['marca'] ?? '-') ?></td></tr>
        <tr><th>Modelo</th><td><?= htmlspecialchars($equipo['modelo'] ?? '-') ?></td></tr>
        <tr><th>Serial</th><td><?= htmlspecialchars($equipo['serial'] ?? '-') ?></td></tr>
        <tr><th>Descripción</th><td><?= nl2br(htmlspecialchars($equipo['descripcion'] ?? '-')) ?></td></tr>
    </table>
</div>
