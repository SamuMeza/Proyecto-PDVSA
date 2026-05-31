<h2>Dashboard de Cumplimiento</h2>
<div class="card-grid">
    <div class="card">
        <h3>Órdenes Preventivas</h3>
        <p class="stat"><?= $stats['preventivas_completadas'] ?? 0 ?> / <?= $stats['preventivas_total'] ?? 0 ?></p>
    </div>
    <div class="card">
        <h3>Órdenes Correctivas</h3>
        <p class="stat"><?= $stats['correctivas_abiertas'] ?? 0 ?> abiertas</p>
    </div>
</div>
