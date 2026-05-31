<h2>Reporte de Condición</h2>
<form method="POST" action="<?= \App\Core\App::BASE_PATH ?>/correctivas?action=condicion">
    <div class="form-group">
        <label>Equipo *</label>
        <select name="equipo_id" required>
            <?php foreach ($equipos as $eq): ?>
                <option value="<?= $eq['id'] ?>"><?= htmlspecialchars($eq['numero_activo_fijo'] . ' - ' . $eq['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Condición Observada *</label>
        <textarea name="condicion_observada" rows="4" required></textarea>
    </div>
    <div class="form-group">
        <label>Recomendación</label>
        <textarea name="recomendacion_condicion" rows="3"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Guardar Reporte</button>
</form>
