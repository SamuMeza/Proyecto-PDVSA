<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Sistema PDVSA') ?> — Sistema PDVSA</title>
    <link rel="stylesheet" href="<?= \App\Core\App::BASE_PATH ?>/public/assets/css/main.css">
</head>
<body class="auth-page">
    <div class="auth-card <?= $authCardWide ?? '' ?>">
        <button type="button" class="theme-toggle auth-theme-toggle" aria-label="Cambiar tema">
            <span class="theme-icon-light">☀</span>
            <span class="theme-icon-dark">☽</span>
        </button>
