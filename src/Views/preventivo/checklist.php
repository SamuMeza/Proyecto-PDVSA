<h3>Checklist de Ejecución</h3>
<form method="POST" action="<?= \App\Core\App::BASE_PATH ?>/preventivas?action=checklist&id=<?= $ordenId ?>">
    <?php foreach ($checklistItems as $item): ?>
    <div class="form-group checkbox-group">
        <label>
            <input type="checkbox" name="items[<?= $item['id'] ?>]" value="1">
            <?= htmlspecialchars($item['descripcion_tarea']) ?>
            <?php if ($item['es_obligatorio']): ?><span class="badge badge-required">Obligatorio</span><?php endif; ?>
        </label>
        <textarea name="observaciones[<?= $item['id'] ?>]" placeholder="Observaciones..." rows="2"></textarea>
    </div>
    <?php endforeach; ?>
    <button type="submit" class="btn btn-primary">Guardar</button>
</form>
