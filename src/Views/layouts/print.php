<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Imprimir' ?></title>
    <link rel="stylesheet" href="<?= \App\Core\App::BASE_PATH ?>/public/assets/css/main.css">
    <style>
        body { font-family: 'Inter', sans-serif; font-size: 12pt; padding: 20px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <?= $content ?? '' ?>
    <script>window.print();</script>
</body>
</html>
