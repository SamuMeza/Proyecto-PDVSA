<h2>Registrar Equipo</h2>
<form method="POST" action="<?= \App\Core\App::BASE_PATH ?>/public/equipos.php?action=store">
    <div class="form-group">
        <label>Número de Activo Fijo *</label>
        <input type="text" name="numero_activo_fijo" required>
    </div>
    <div class="form-group">
        <label>Nombre del Equipo *</label>
        <input type="text" name="nombre" required>
    </div>
    <div class="form-group">
        <label>Marca</label>
        <input type="text" name="marca">
    </div>
    <div class="form-group">
        <label>Modelo</label>
        <input type="text" name="modelo">
    </div>
    <div class="form-group">
        <label>Serial</label>
        <input type="text" name="serial">
    </div>
    <div class="form-group">
        <label>Categoría *</label>
        <select name="categoria_id" required>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Zona *</label>
        <select name="zona_id" required>
            <?php foreach ($zonas as $z): ?>
                <option value="<?= $z['id'] ?>"><?= htmlspecialchars($z['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Grupo de Seguridad</label>
        <select name="grupo_seguridad_id">
            <option value="">Seleccione...</option>
            <?php foreach ($gruposSeguridad as $g): ?>
                <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Descripción</label>
        <textarea name="descripcion" rows="3"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="<?= \App\Core\App::BASE_PATH ?>/public/equipos.php" class="btn btn-secondary">Cancelar</a>
</form>
