<div class="page-header">
    <h1 class="page-title">Usuarios</h1>
    <?php if (\App\Services\AuthService::hasPermission('usuarios', 'crear')): ?>
        <a href="<?= \App\Core\App::BASE_PATH ?>/usuarios/crear" class="btn btn-primary">Nuevo Usuario</a>
    <?php endif; ?>
</div>

<div class="page-card">
    <form method="GET" action="<?= \App\Core\App::BASE_PATH ?>/usuarios" class="filter-panel">
        <div class="form-group">
            <label for="search">Buscar</label>
            <input type="text" id="search" name="search" placeholder="Nombre o usuario..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="form-group">
            <label for="rol">Rol</label>
            <select id="rol" name="rol">
                <option value="">Todos los roles</option>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= $filtroRol === (int)$r['id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="estado">Estado</label>
            <select id="estado" name="estado">
                <option value="">Todos los estados</option>
                <option value="activo" <?= $filtroEstado === 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= $filtroEstado === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>
        <div class="form-group" style="align-self:end;">
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    <table class="data-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr><td colspan="5">No se encontraron usuarios.</td></tr>
            <?php else: ?>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                    <td><?= htmlspecialchars($u['nombre_usuario']) ?></td>
                    <td><?= htmlspecialchars($u['rol_nombre']) ?></td>
                    <td>
                        <span class="tag <?= $u['estado'] === 'activo' ? 'tag-active' : 'tag-inactive' ?>">
                            <?= ucfirst($u['estado']) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <?php if (\App\Services\AuthService::hasPermission('usuarios', 'editar')): ?>
                            <a href="<?= \App\Core\App::BASE_PATH ?>/usuarios/editar/<?= $u['id'] ?>" class="btn btn-outline small-action">Editar</a>
                            <?php if ((int)$u['id'] !== (int)\App\Core\Session::get('usuario_id')): ?>
                                <form method="POST" action="<?= \App\Core\App::BASE_PATH ?>/usuarios/toggle" style="display:inline">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn btn-outline small-action" onclick="return confirm('<?= $u['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?> este usuario?')">
                                        <?= $u['estado'] === 'activo' ? 'Desactivar' : 'Activar' ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&rol=<?= $filtroRol ?>&estado=<?= urlencode($filtroEstado) ?>" class="pagination-link">&laquo; Anterior</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&rol=<?= $filtroRol ?>&estado=<?= urlencode($filtroEstado) ?>"
               class="pagination-link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&rol=<?= $filtroRol ?>&estado=<?= urlencode($filtroEstado) ?>" class="pagination-link">Siguiente &raquo;</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
