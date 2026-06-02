<div class="page-header">
    <h1 class="page-title">Reportes</h1>
</div>

<div class="card-grid">
    <div class="card">
        <h3>Cumplimiento preventivo</h3>
        <p class="stat"><?= $stats['cumplimiento_pct'] ?>%</p>
        <p>de ordenes preventivas cerradas</p>
    </div>
    <div class="card">
        <h3>Preventivas este mes</h3>
        <p class="stat"><?= $stats['preventivas_mes'] ?></p>
        <p>completadas en el mes actual</p>
    </div>
    <div class="card">
        <h3>Correctivas abiertas</h3>
        <p class="stat"><?= $stats['correctivas_abiertas'] ?></p>
        <p>reportadas o en progreso</p>
    </div>
    <div class="card">
        <h3>Equipos registrados</h3>
        <p class="stat"><?= $stats['equipos_total'] ?></p>
        <p><?= $stats['usuarios_activos'] ?> usuarios activos</p>
    </div>
</div>

<div class="page-card" style="margin-bottom:1rem;">
    <h2 style="margin-bottom:1rem;">Resumen general</h2>
    <table class="data-table">
        <tbody>
            <tr><td>Ordenes preventivas totales</td><td><strong><?= $stats['preventivas_total'] ?></strong></td></tr>
            <tr><td>Ordenes preventivas completadas</td><td><strong><?= $stats['preventivas_completadas'] ?></strong></td></tr>
            <tr><td>Ordenes correctivas totales</td><td><strong><?= $stats['correctivas_total'] ?></strong></td></tr>
            <tr><td>Ordenes correctivas abiertas</td><td><strong><?= $stats['correctivas_abiertas'] ?></strong></td></tr>
            <tr><td>Equipos registrados</td><td><strong><?= $stats['equipos_total'] ?></strong></td></tr>
        </tbody>
    </table>
</div>

<div class="page-card" style="margin-bottom:1rem;">
    <h2 style="margin-bottom:1rem;">Generar Reportes</h2>
    <p style="color:var(--text-muted); margin-bottom:1rem;">Genere reportes PDF y CSV con filtros configurables.</p>
    <a href="<?= \App\Core\App::BASE_PATH ?>/reportes/generar" class="btn btn-primary">Generar Reporte PDF</a>
    <a href="<?= \App\Core\App::BASE_PATH ?>/reportes/historial" class="btn btn-secondary">Ver Historial</a>
</div>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:1rem;">

    <div class="page-card">
        <h3 style="margin-bottom:0.5rem;">Reporte de cumplimiento</h3>
        <p style="color:var(--text-muted); margin-bottom:1rem; font-size:0.9em;">Cumplimiento mensual de ordenes preventivas y resumen de correctivas.</p>
        <a href="<?= \App\Core\App::BASE_PATH ?>/reportes/cumplimiento" class="btn btn-primary" style="width:100%; text-align:center;">Ver reporte</a>
    </div>

    <div class="page-card">
        <h3 style="margin-bottom:0.5rem;">Estadisticas de fallas</h3>
        <p style="color:var(--text-muted); margin-bottom:1rem; font-size:0.9em;">Distribucion de fallas por tipo, zona y mes con graficos.</p>
        <a href="<?= \App\Core\App::BASE_PATH ?>/reportes/fallas" class="btn btn-primary" style="width:100%; text-align:center;">Ver reporte</a>
    </div>

    <div class="page-card">
        <h3 style="margin-bottom:0.5rem;">Resumen mensual</h3>
        <p style="color:var(--text-muted); margin-bottom:1rem; font-size:0.9em;">Detalle mensual de preventivas: completadas, en curso y suspendidas.</p>
        <a href="<?= \App\Core\App::BASE_PATH ?>/reportes/resumen-mensual" class="btn btn-primary" style="width:100%; text-align:center;">Ver reporte</a>
    </div>

    <div class="page-card">
        <h3 style="margin-bottom:0.5rem;">Rendimiento por tecnico</h3>
        <p style="color:var(--text-muted); margin-bottom:1rem; font-size:0.9em;">Desempeno individual de mantenedores: asignadas, completadas y cumplimiento.</p>
        <a href="<?= \App\Core\App::BASE_PATH ?>/reportes/tecnicos" class="btn btn-primary" style="width:100%; text-align:center;">Ver reporte</a>
    </div>

</div>
