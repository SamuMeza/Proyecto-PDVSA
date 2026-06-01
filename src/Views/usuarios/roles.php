<div class="page-header">
    <h1 class="page-title">Roles y Permisos</h1>
</div>

<div class="page-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($roles)): ?>
                <tr><td colspan="3">No hay roles registrados.</td></tr>
            <?php else: ?>
                <?php foreach ($roles as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['nombre']) ?></td>
                    <td><?= htmlspecialchars($r['descripcion'] ?? '') ?></td>
                    <td>
                        <span class="badge badge-<?= $r['estado'] === 'activo' ? 'success' : 'danger' ?>">
                            <?= ucfirst($r['estado']) ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
