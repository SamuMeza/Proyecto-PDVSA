<h2>Reportar Falla</h2>
<form method="POST" action="<?= \App\Core\App::BASE_PATH ?>/correctivas?action=store" enctype="multipart/form-data">
    <div class="form-group">
        <label>Equipo *</label>
        <select name="equipo_id" required>
            <?php foreach ($equipos as $eq): ?>
                <option value="<?= $eq['id'] ?>"><?= htmlspecialchars($eq['numero_activo_fijo'] . ' - ' . $eq['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Tipo de Falla *</label>
        <select name="tipo_falla_id" required>
            <?php foreach ($tiposFalla as $tf): ?>
                <option value="<?= $tf['id'] ?>"><?= htmlspecialchars($tf['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Prioridad *</label>
        <select name="prioridad_id" required>
            <?php foreach ($prioridades as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Descripción de la Falla *</label>
        <textarea name="descripcion_falla" rows="4" required></textarea>
    </div>
    <div class="form-group">
        <label>Fotos</label>
        <input type="file" name="fotos[]" multiple accept="image/*">
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="es_reporte_condicion" value="1"> Reporte de Condición</label>
    </div>
    <button type="submit" class="btn btn-danger">Reportar Falla</button>
</form>
