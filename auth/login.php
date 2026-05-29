<?php
require_once __DIR__ . '/auth_functions.php';

iniciarSesionPhp();

if (estaAutenticado()) {
    header('Location: ' . BASE_PATH . '/public/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['nombre_usuario'] ?? '';
    $clave   = $_POST['contrasena'] ?? '';
    $resultado = intentarLogin($usuario, $clave);

    if ($resultado['ok']) {
        header('Location: ' . BASE_PATH . '/public/index.php');
        exit;
    }
    $error = $resultado['error'];
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — Sistema PDVSA</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/css/styles.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <h1>Iniciar sesión</h1>
        <p class="auth-subtitle">Sistema de gestión PDVSA</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form">
            <label for="nombre_usuario">Usuario</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required autocomplete="username"
                   value="<?= htmlspecialchars($_POST['nombre_usuario'] ?? '') ?>">

            <label for="contrasena">Contraseña</label>
            <input type="password" id="contrasena" name="contrasena" required autocomplete="current-password">

            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>

        <button type="button" class="theme-toggle auth-theme-toggle" aria-label="Cambiar tema">
            <span class="theme-icon-light">☀</span>
            <span class="theme-icon-dark">☽</span>
        </button>
    </div>
    <script src="<?= BASE_PATH ?>/css/theme.js"></script>
</body>
</html>
