<div class="page-header">
    <h1 class="page-title">Editar Usuario</h1>
</div>

<div class="page-card">
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= \App\Core\App::BASE_PATH ?>/usuarios/editar/<?= $usuario['id'] ?>" class="form-grid">
        <div class="form-group">
            <label>Nombre Completo *</label>
            <input type="text" name="nombre_completo" value="<?= htmlspecialchars($usuario['nombre_completo']) ?>" required>
        </div>
        <div class="form-group">
            <label>Nombre de Usuario</label>
            <input type="text" value="<?= htmlspecialchars($usuario['nombre_usuario']) ?>" disabled>
        </div>
        <div class="form-group">
            <label>Rol *</label>
            <select name="rol_id" required>
                <?php foreach ($roles as $r): ?>
                    <option value="<?= $r['id'] ?>" <?= (int)$r['id'] === (int)$usuario['rol_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($r['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Estado</label>
            <select name="estado">
                <option value="activo" <?= $usuario['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
                <option value="inactivo" <?= $usuario['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
            </select>
        </div>
        <div class="form-group" style="grid-column:1 / -1; text-align:right;">
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="<?= \App\Core\App::BASE_PATH ?>/usuarios" class="btn btn-outline">Cancelar</a>
            </div>
        </div>
    </form>
</div>
