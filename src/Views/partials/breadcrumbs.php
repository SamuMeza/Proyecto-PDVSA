<nav class="breadcrumbs" aria-label="Breadcrumb">
    <ol>
        <li><a href="<?= \App\Core\App::BASE_PATH ?>/public/index.php">Inicio</a></li>
        <?php foreach ($breadcrumbs ?? [] as $crumb): ?>
            <li><?php if (!empty($crumb['url'])): ?><a href="<?= $crumb['url'] ?>"><?= htmlspecialchars($crumb['label']) ?></a><?php else: ?><span><?= htmlspecialchars($crumb['label']) ?></span><?php endif; ?></li>
        <?php endforeach; ?>
    </ol>
</nav>
