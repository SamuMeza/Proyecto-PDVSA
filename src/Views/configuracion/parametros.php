<h2>Parámetros del Sistema</h2>
<form method="POST" action="<?= \App\Core\App::BASE_PATH ?>/configuracion/parametros/update">
    <?php foreach ($configs as $c): ?>
    <div class="form-group">
        <label><?= htmlspecialchars($c['clave']) ?></label>
        <?php if ($c['clave'] === 'ruta_logo_pdvsa'): ?>
            <input type="text" name="config_<?= $c['clave'] ?>" value="<?= htmlspecialchars($c['valor']) ?>" class="form-control">
        <?php else: ?>
            <input type="text" name="config_<?= $c['clave'] ?>" value="<?= htmlspecialchars($c['valor']) ?>" class="form-control">
        <?php endif; ?>
        <small><?= htmlspecialchars($c['descripcion'] ?? '') ?></small>
    </div>
    <?php endforeach; ?>
    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
</form>
