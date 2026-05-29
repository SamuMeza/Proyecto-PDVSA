<?php
require_once __DIR__ . '/auth_functions.php';

iniciarSesionPhp();

if (isset($_SESSION['usuario_id']) && isset($_SESSION['sesion_token']) && estaAutenticado()) {
    header('Location: ' . BASE_PATH . '/public/index.php');
    exit;
}

if (empty($_SESSION['pending_otp_user_id'])) {
    header('Location: ' . BASE_PATH . '/auth/login.php');
    exit;
}

$error = '';
$mensaje = 'Ingrese el código OTP de 6 dígitos enviado al usuario.';
$usuarioId = (int) $_SESSION['pending_otp_user_id'];
$usuario = usuarioPorId($usuarioId);

if (!$usuario) {
    cerrarSesionUsuario();
    header('Location: ' . BASE_PATH . '/auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo'] ?? '');
    $resultado = validarCodigoOtp($usuarioId, $codigo);

    if ($resultado['ok']) {
        iniciarSesionUsuario($usuario);
        unset($_SESSION['pending_otp_user_id'], $_SESSION['pending_otp_expires']);
        header('Location: ' . BASE_PATH . '/public/index.php');
        exit;
    }

    $error = $resultado['error'];
}

$codigoDebug = '';
$otpRow = obtenerOtpUsuario($usuarioId);
if ($otpRow) {
    $codigoDebug = $otpRow['codigo'];
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validar OTP — Sistema PDVSA</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/css/styles.css">
</head>
<body class="auth-page">
    <div class="auth-card auth-card-wide">
        <h1>Verificación OTP</h1>
        <p class="auth-subtitle">Completa la autenticación con el código temporal de 6 dígitos.</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="alert alert-success" style="margin-bottom: 1rem;">
            <?= htmlspecialchars($mensaje) ?>
        </div>

        <form method="post" class="auth-form">
            <label for="codigo">Código OTP</label>
            <input type="text" id="codigo" name="codigo" required maxlength="6" pattern="\d{6}" autocomplete="one-time-code">
            <button type="submit" class="btn btn-primary">Validar OTP</button>
        </form>

        <?php if ($codigoDebug): ?>
            <div class="alert alert-success" style="margin-top: 1rem;">
                <strong>OTP de prueba:</strong> <?= htmlspecialchars($codigoDebug) ?>
            </div>
        <?php endif; ?>

        <button type="button" class="theme-toggle auth-theme-toggle" aria-label="Cambiar tema">
            <span class="theme-icon-light">☀</span>
            <span class="theme-icon-dark">☽</span>
        </button>
    </div>
    <script src="<?= BASE_PATH ?>/css/theme.js"></script>
</body>
</html>
