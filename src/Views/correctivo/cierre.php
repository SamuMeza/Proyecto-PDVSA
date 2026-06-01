<h2>Cierre de Orden Correctiva</h2>
<form method="POST" action="<?= \App\Core\App::BASE_PATH ?>/correctivas?action=close&id=<?= $orden['id'] ?>">
    <div class="form-group">
        <label>Acciones Tomadas *</label>
        <textarea name="acciones_tomadas" rows="4" required><?= htmlspecialchars($orden['acciones_tomadas'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
        <label>Causa Raíz</label>
        <textarea name="causa_raiz" rows="3"><?= htmlspecialchars($orden['causa_raiz'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
        <label>Repuestos Utilizados</label>
        <textarea name="repuestos_utilizados" rows="3"><?= htmlspecialchars($orden['repuestos_utilizados'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
        <label>Fecha Fin Reparación *</label>
        <input type="date" name="fecha_fin_reparacion" required value="<?= date('Y-m-d') ?>">
    </div>
    <div class="form-group">
        <label>Hora Fin Reparación *</label>
        <input type="time" name="hora_fin_reparacion" required value="<?= date('H:i') ?>">
    </div>
    <button type="submit" class="btn btn-primary">Cerrar Orden</button>
</form>
