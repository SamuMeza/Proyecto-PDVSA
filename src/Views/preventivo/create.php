<h2>Crear Orden Preventiva Manual</h2>
<form method="POST" action="<?= \App\Core\App::BASE_PATH ?>/public/preventivas.php?action=store">
    <div class="form-group">
        <label>Equipo *</label>
        <select name="equipo_id" required>
            <?php foreach ($equipos as $eq): ?>
                <option value="<?= $eq['id'] ?>"><?= htmlspecialchars($eq['numero_activo_fijo'] . ' - ' . $eq['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Nivel de Mantenimiento *</label>
        <select name="nivel_mantenimiento_id" required>
            <?php foreach ($niveles as $n): ?>
                <option value="<?= $n['id'] ?>"><?= htmlspecialchars($n['nombre_nivel']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Fecha Planificada *</label>
        <input type="date" name="fecha_planificada" required>
    </div>
    <button type="submit" class="btn btn-primary">Crear</button>
    <a href="<?= \App\Core\App::BASE_PATH ?>/public/preventivas.php" class="btn btn-secondary">Cancelar</a>
</form>
