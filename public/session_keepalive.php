<?php
require_once __DIR__ . '/../auth/auth_functions.php';

iniciarSesionPhp();
header('Content-Type: application/json');

if (!estaAutenticado()) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'No autenticado']);
    exit;
}

$usuarioId = (int) ($_SESSION['usuario_id'] ?? 0);
$token = $_SESSION['sesion_token'] ?? '';

if (!$usuarioId || !$token) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Sesión inválida']);
    exit;
}

if (!renovarSesion($usuarioId, $token)) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'No se pudo renovar la sesión']);
    exit;
}

$usuario = usuarioPorId($usuarioId);
$expira = $usuario['sesion_expira_en'] ?? null;

echo json_encode([
    'ok' => true,
    'expires_at' => $expira,
]);
