<h2>Editar Usuario</h2>
<form method="POST" action="<?= \App\Core\App::BASE_PATH ?>/public/usuarios.php?action=update&id=<?= $usuario['id'] ?>">
    <div class="form-group">
        <label>Nombre Completo *</label>
        <input type="text" name="nombre_completo" value="<?= htmlspecialchars($usuario['nombre_completo']) ?>" required>
    </div>
    <div class="form-group">
        <label>Rol *</label>
        <select name="rol_id" required>
            <?php foreach ($roles as $r): ?>
                <option value="<?= $r['id'] ?>" <?= $r['id'] === $usuario['rol_id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['nombre']) ?></option>
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
    <button type="submit" class="btn btn-primary">Actualizar</button>
</form>
