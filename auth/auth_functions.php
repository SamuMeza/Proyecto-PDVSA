<?php
/**
 * Funciones de autenticación y sesión.
 */

require_once dirname(__DIR__) . '/config/db.php';

const SESION_HORAS = 8;
const BASE_PATH = '/sistema_pdvsa';
const ROL_ADMINISTRADOR = 'Administrador';

function iniciarSesionPhp(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => SESION_HORAS * 3600,
            'path'     => BASE_PATH . '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

function generarTokenSesion(): string
{
    return bin2hex(random_bytes(32));
}

function usuarioPorNombre(string $nombreUsuario): ?array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        'SELECT u.*, r.nombre AS rol_nombre
         FROM usuarios u
         INNER JOIN roles r ON r.id = u.rol_id
         WHERE u.nombre_usuario = ? AND u.estado = ?'
    );
    $stmt->execute([$nombreUsuario, 'activo']);
    $row = $stmt->fetch();
    return $row ?: null;
}

function usuarioPorId(int $id): ?array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        'SELECT u.*, r.nombre AS rol_nombre
         FROM usuarios u
         INNER JOIN roles r ON r.id = u.rol_id
         WHERE u.id = ? AND u.estado = ?'
    );
    $stmt->execute([$id, 'activo']);
    $row = $stmt->fetch();
    return $row ?: null;
}

function registrarSesionEnBd(int $usuarioId, string $token): void
{
    $pdo = getDbConnection();
    $expira = date('Y-m-d H:i:s', time() + SESION_HORAS * 3600);
    $stmt = $pdo->prepare(
        'UPDATE usuarios
         SET sesion_activa_token = ?, sesion_expira_en = ?, ultimo_acceso = NOW(), actualizado_en = NOW()
         WHERE id = ?'
    );
    $stmt->execute([$token, $expira, $usuarioId]);
}

function cerrarSesionEnBd(int $usuarioId): void
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        'UPDATE usuarios
         SET sesion_activa_token = NULL, sesion_expira_en = NULL, actualizado_en = NOW()
         WHERE id = ?'
    );
    $stmt->execute([$usuarioId]);
}

function validarSesionBd(int $usuarioId, string $token): bool
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        'SELECT id FROM usuarios
         WHERE id = ? AND sesion_activa_token = ? AND sesion_expira_en > NOW() AND estado = ?'
    );
    $stmt->execute([$usuarioId, $token, 'activo']);
    return (bool) $stmt->fetch();
}

function iniciarSesionUsuario(array $usuario): void
{
    iniciarSesionPhp();
    $token = generarTokenSesion();
    registrarSesionEnBd((int) $usuario['id'], $token);

    $_SESSION['usuario_id']      = (int) $usuario['id'];
    $_SESSION['nombre_usuario']  = $usuario['nombre_usuario'];
    $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
    $_SESSION['rol_id']          = (int) $usuario['rol_id'];
    $_SESSION['rol_nombre']        = $usuario['rol_nombre'] ?? '';
    $_SESSION['sesion_token']      = $token;
}

function cerrarSesionUsuario(): void
{
    iniciarSesionPhp();
    if (!empty($_SESSION['usuario_id']) && !empty($_SESSION['sesion_token'])) {
        cerrarSesionEnBd((int) $_SESSION['usuario_id']);
    }
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function estaAutenticado(): bool
{
    iniciarSesionPhp();
    if (empty($_SESSION['usuario_id']) || empty($_SESSION['sesion_token'])) {
        return false;
    }
    return validarSesionBd((int) $_SESSION['usuario_id'], $_SESSION['sesion_token']);
}

function requerirAutenticacion(): void
{
    if (!estaAutenticado()) {
        header('Location: ' . BASE_PATH . '/auth/login.php');
        exit;
    }
}

function esAdministrador(): bool
{
    if (!estaAutenticado()) {
        return false;
    }
    return ($_SESSION['rol_nombre'] ?? '') === ROL_ADMINISTRADOR;
}

function requerirAdministrador(): void
{
    requerirAutenticacion();
    if (!esAdministrador()) {
        header('Location: ' . BASE_PATH . '/public/index.php?error=sin_permiso');
        exit;
    }
}

function rolesDisponibles(): array
{
    $pdo = getDbConnection();
    return $pdo->query("SELECT id, nombre FROM roles WHERE estado = 'activo' ORDER BY id")->fetchAll();
}

function crearUsuario(array $datos): array
{
    $requeridos = ['rol_id', 'nombre_completo', 'nombre_usuario', 'contrasena'];
    foreach ($requeridos as $campo) {
        if (empty($datos[$campo])) {
            return ['ok' => false, 'error' => 'Complete todos los campos obligatorios.'];
        }
    }

    if (strlen($datos['contrasena']) < 6) {
        return ['ok' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres.'];
    }

    $pdo = getDbConnection();

    $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE nombre_usuario = ?');
    $stmt->execute([$datos['nombre_usuario']]);
    if ($stmt->fetch()) {
        return ['ok' => false, 'error' => 'El nombre de usuario ya está registrado.'];
    }

    $stmt = $pdo->prepare('SELECT id FROM roles WHERE id = ? AND estado = ?');
    $stmt->execute([(int) $datos['rol_id'], 'activo']);
    if (!$stmt->fetch()) {
        return ['ok' => false, 'error' => 'Rol no válido.'];
    }

    $hash = password_hash($datos['contrasena'], PASSWORD_BCRYPT);

    $creadoPor = !empty($datos['creado_por']) ? (int) $datos['creado_por'] : null;

    $stmt = $pdo->prepare(
        'INSERT INTO usuarios (rol_id, nombre_completo, cargo, email, telefono_extension, nombre_usuario, contrasena_hash, estado, creado_por)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        (int) $datos['rol_id'],
        trim($datos['nombre_completo']),
        $datos['cargo'] ?? null,
        $datos['email'] ?? null,
        $datos['telefono_extension'] ?? null,
        trim($datos['nombre_usuario']),
        $hash,
        'activo',
        $creadoPor,
    ]);

    return ['ok' => true, 'id' => (int) $pdo->lastInsertId()];
}

function intentarLogin(string $nombreUsuario, string $contrasena): array
{
    $usuario = usuarioPorNombre(trim($nombreUsuario));
    if (!$usuario) {
        return ['ok' => false, 'error' => 'Usuario o contraseña incorrectos.'];
    }

    if (!password_verify($contrasena, $usuario['contrasena_hash'])) {
        return ['ok' => false, 'error' => 'Usuario o contraseña incorrectos.'];
    }

    iniciarSesionUsuario($usuario);
    return ['ok' => true];
}
