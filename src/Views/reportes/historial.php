<div class="page-header">
    <h1 class="page-title">Historial de Reportes</h1>
</div>

<div class="page-card">
    <form id="filtro-historial" method="GET" action="<?= \App\Core\App::BASE_PATH ?>/reportes/historial">
        <div class="form-row">
            <div class="form-group">
                <label for="tipo_reporte">Tipo de Reporte</label>
                <select id="tipo_reporte" name="tipo_reporte">
                    <option value="">Todos</option>
                    <option value="fallas" <?= $filtros['tipo_reporte'] === 'fallas' ? 'selected' : '' ?>>Fallas</option>
                    <option value="cumplimiento" <?= $filtros['tipo_reporte'] === 'cumplimiento' ? 'selected' : '' ?>>Cumplimiento</option>
                    <option value="resumen-mensual" <?= $filtros['tipo_reporte'] === 'resumen-mensual' ? 'selected' : '' ?>>Resumen Mensual</option>
                    <option value="tecnicos" <?= $filtros['tipo_reporte'] === 'tecnicos' ? 'selected' : '' ?>>Técnicos</option>
                </select>
            </div>
            <div class="form-group">
                <label for="estado">Estado</label>
                <select id="estado" name="estado">
                    <option value="">Todos</option>
                    <option value="completado" <?= $filtros['estado'] === 'completado' ? 'selected' : '' ?>>Completado</option>
                    <option value="error" <?= $filtros['estado'] === 'error' ? 'selected' : '' ?>>Error</option>
                </select>
            </div>
            <div class="form-group">
                <label for="fecha_desde">Fecha Desde</label>
                <input type="date" id="fecha_desde" name="fecha_desde" value="<?= htmlspecialchars($filtros['fecha_desde'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="fecha_hasta">Fecha Hasta</label>
                <input type="date" id="fecha_hasta" name="fecha_hasta" value="<?= htmlspecialchars($filtros['fecha_hasta'] ?? '') ?>">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="<?= \App\Core\App::BASE_PATH ?>/reportes/historial" class="btn btn-secondary">Limpiar</a>
        </div>
    </form>
</div>

<?php if (empty($historial['reportes'])): ?>
    <div class="page-card">
        <p style="text-align:center; color:var(--text-muted);">No hay reportes generados</p>
    </div>
<?php else: ?>
    <div class="page-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historial['reportes'] as $index => $reporte): ?>
                    <tr>
                        <td><?= ($historial['pagina_actual'] - 1) * 20 + $index + 1 ?></td>
                        <td><?= htmlspecialchars(ucfirst(str_replace('-', ' ', $reporte['tipo_reporte']))) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($reporte['creado_en'])) ?></td>
                        <td>
                            <span class="badge badge-<?= $reporte['estado'] === 'completado' ? 'success' : 'danger' ?>">
                                <?= ucfirst($reporte['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($reporte['estado'] === 'completado'): ?>
                                <a href="<?= \App\Core\App::BASE_PATH ?>/reportes/descargar/<?= $reporte['id'] ?>?formato=pdf" 
                                   class="btn btn-sm btn-primary" title="Descargar PDF">PDF</a>
                                <a href="<?= \App\Core\App::BASE_PATH ?>/reportes/descargar/<?= $reporte['id'] ?>?formato=csv" 
                                   class="btn btn-sm btn-secondary" title="Descargar CSV">CSV</a>
                            <?php endif; ?>
                            <button type="button" class="btn btn-sm btn-danger" 
                                    onclick="eliminarReporte(<?= $reporte['id'] ?>)" title="Eliminar">✕</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <?php if ($historial['total_paginas'] > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $historial['total_paginas']; $i++): ?>
                <a href="?pagina=<?= $i ?>&tipo_reporte=<?= urlencode($filtros['tipo_reporte'] ?? '') ?>&estado=<?= urlencode($filtros['estado'] ?? '') ?>&fecha_desde=<?= urlencode($filtros['fecha_desde'] ?? '') ?>&fecha_hasta=<?= urlencode($filtros['fecha_hasta'] ?? '') ?>"
                   class="page-link <?= $i === $historial['pagina_actual'] ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<script>
function eliminarReporte(id) {
    if (!confirm('¿Está seguro de eliminar este reporte?')) {
        return;
    }
    
    fetch('<?= \App\Core\App::BASE_PATH ?>/reportes/eliminar/' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            location.reload();
        } else {
            alert('Error al eliminar el reporte');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar el reporte');
    });
}
</script>
