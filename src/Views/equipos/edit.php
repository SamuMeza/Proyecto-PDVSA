<h2>Editar Equipo</h2>
<form method="POST" action="<?= \App\Core\App::BASE_PATH ?>/public/equipos.php?action=update&id=<?= $equipo['id'] ?>">
    <div class="form-group">
        <label>Número de Activo Fijo *</label>
        <input type="text" name="numero_activo_fijo" value="<?= htmlspecialchars($equipo['numero_activo_fijo']) ?>" required>
    </div>
    <div class="form-group">
        <label>Nombre del Equipo *</label>
        <input type="text" name="nombre" value="<?= htmlspecialchars($equipo['nombre']) ?>" required>
    </div>
    <div class="form-group">
        <label>Marca</label>
        <input type="text" name="marca" value="<?= htmlspecialchars($equipo['marca'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Modelo</label>
        <input type="text" name="modelo" value="<?= htmlspecialchars($equipo['modelo'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Estado</label>
        <select name="estado">
            <option value="activo" <?= $equipo['estado'] === 'activo' ? 'selected' : '' ?>>Activo</option>
            <option value="inactivo" <?= $equipo['estado'] === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
    <a href="<?= \App\Core\App::BASE_PATH ?>/public/equipos.php" class="btn btn-secondary">Cancelar</a>
</form>
