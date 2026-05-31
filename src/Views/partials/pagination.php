<?php if ($totalPages > 1): ?>
<nav class="pagination" aria-label="Paginación">
    <ul>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="<?= $i === $currentPage ? 'active' : '' ?>">
                <a href="?page=<?= $i ?><?= isset($filters) ? '&' . http_build_query($filters) : '' ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>
