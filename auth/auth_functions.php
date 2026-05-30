<?php
/**
 * Funciones de autenticación y sesión.
 */

require_once dirname(__DIR__) . '/config/db.php';

const SESION_HORAS = 8;
const BASE_PATH = '/sistema_pdvsa';
const ROL_ADMINISTRADOR = 'Administrador';
const OTP_TOKEN_MINUTES = 5;
const OTP_GENERACION_LIMITE_DIARIO = 150;

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

function obtenerConfiguracionSistema(string $clave, $default = null)
{
    static $cache = [];
    if (array_key_exists($clave, $cache)) {
        return $cache[$clave];
    }

    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('SELECT valor FROM configuracion_sistema WHERE clave = ? LIMIT 1');
        $stmt->execute([$clave]);
        $fila = $stmt->fetch();
        if ($fila) {
            $cache[$clave] = $fila['valor'];
            return $cache[$clave];
        }
    } catch (PDOException $e) {
        return $cache[$clave] = $default;
    }

    return $cache[$clave] = $default;
}

function obtenerSegundosSesionPorRol(string $rolNombre): int
{
    $valores = [
        'Administrador' => obtenerConfiguracionSistema('sesion_minutos_admin', '10'),
        'Supervisor' => obtenerConfiguracionSistema('sesion_minutos_supervisor', '20'),
        'Otros' => obtenerConfiguracionSistema('sesion_minutos_otros', '35'),
    ];

    $minutos = $valores[$rolNombre] ?? $valores['Otros'];
    $minutos = (int) $minutos;
    return max($minutos, 1) * 60;
}

function emailInternoParaUsuario(string $nombreUsuario): string
{
    $dominio = obtenerConfiguracionSistema('email_dominio_interno', 'pdvsa.com');
    $usuarioLimpio = strtolower(trim($nombreUsuario));
    $usuarioLimpio = preg_replace('/[^a-z0-9_.-]/', '', $usuarioLimpio);
    return $usuarioLimpio . '@' . $dominio;
}

function normalizarTelefono58(string $telefono): ?string
{
    $telefono = trim($telefono);
    $telefono = preg_replace('/[^0-9+]/', '', $telefono);
    if (!str_starts_with($telefono, '+58')) {
        $telefono = '+58' . ltrim($telefono, '0');
    }

    if (!preg_match('/^\+58[0-9]{7,14}$/', $telefono)) {
        return null;
    }

    return $telefono;
}

function usuarioPorNombre(string $nombreUsuario): ?array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare(
        'SELECT u.*, r.nombre AS rol_nombre, r.permisos_json
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
        'SELECT u.*, r.nombre AS rol_nombre, r.permisos_json
         FROM usuarios u
         INNER JOIN roles r ON r.id = u.rol_id
         WHERE u.id = ? AND u.estado = ?'
    );
    $stmt->execute([$id, 'activo']);
    $row = $stmt->fetch();
    return $row ?: null;
}

function registrarSesionEnBd(int $usuarioId, string $token, string $rolNombre): void
{
    $pdo = getDbConnection();
    $expira = date('Y-m-d H:i:s', time() + obtenerSegundosSesionPorRol($rolNombre));
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
    registrarSesionEnBd((int) $usuario['id'], $token, $usuario['rol_nombre'] ?? 'Otros');

    $_SESSION['usuario_id']      = (int) $usuario['id'];
    $_SESSION['nombre_usuario']  = $usuario['nombre_usuario'];
    $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
    $_SESSION['rol_id']          = (int) $usuario['rol_id'];
    $_SESSION['rol_nombre']      = $usuario['rol_nombre'] ?? '';
    $_SESSION['sesion_token']    = $token;
    $_SESSION['permisos']        = json_decode($usuario['permisos_json'] ?? '{}', true) ?: [];
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

    $telefono = $datos['telefono_extension'] ?? '';
    if ($telefono !== '') {
        $telefono = normalizarTelefono58($telefono);
        if ($telefono === null) {
            return ['ok' => false, 'error' => 'El teléfono debe comenzar con +58 y contener sólo números válidos.'];
        }
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
    $emailInterno = emailInternoParaUsuario(trim($datos['nombre_usuario']));
    $creadoPor = !empty($datos['creado_por']) ? (int) $datos['creado_por'] : null;

    $stmt = $pdo->prepare(
        'INSERT INTO usuarios (rol_id, nombre_completo, cargo, email, telefono_extension, nombre_usuario, contrasena_hash, estado, creado_por)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        (int) $datos['rol_id'],
        trim($datos['nombre_completo']),
        $datos['cargo'] ?? null,
        $emailInterno,
        $telefono,
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

    $resultadoOtp = generarOtpParaUsuario($usuario);
    if (!$resultadoOtp['ok']) {
        return $resultadoOtp;
    }

    iniciarSesionPhp();
    $_SESSION['pending_otp_user_id'] = (int) $usuario['id'];
    $_SESSION['pending_otp_usuario'] = $usuario['nombre_usuario'];
    $_SESSION['pending_otp_expires'] = time() + (OTP_TOKEN_MINUTES * 60);

    return ['ok' => true, 'needs_otp' => true];
}

function generarOtpParaUsuario(array $usuario): array
{
    $usuarioId = (int) $usuario['id'];
    $otp = obtenerOtpUsuario($usuarioId);
    $hoy = date('Y-m-d');
    $generadosHoy = 0;

    if ($otp && $otp['fecha_ultimo_generado'] === $hoy) {
        $generadosHoy = (int) $otp['generados_hoy'];
    }

    if ($generadosHoy >= OTP_GENERACION_LIMITE_DIARIO) {
        return ['ok' => false, 'error' => 'Se alcanzó el límite diario de generación de códigos OTP.'];
    }

    $codigo = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiracion = date('Y-m-d H:i:s', time() + OTP_TOKEN_MINUTES * 60);
    $generadosHoy++;

    $pdo = getDbConnection();
    if ($otp) {
        $stmt = $pdo->prepare(
            'UPDATE usuario_otp
             SET codigo = ?, expiracion_en = ?, generados_hoy = ?, fecha_ultimo_generado = ?, intentos_fallidos = 0, actualizado_en = NOW()
             WHERE usuario_id = ?'
        );
        $stmt->execute([$codigo, $expiracion, $generadosHoy, $hoy, $usuarioId]);
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO usuario_otp (usuario_id, codigo, expiracion_en, generados_hoy, fecha_ultimo_generado, intentos_fallidos)
             VALUES (?, ?, ?, ?, ?, 0)'
        );
        $stmt->execute([$usuarioId, $codigo, $expiracion, $generadosHoy, $hoy]);
    }

    return ['ok' => true, 'codigo' => $codigo];
}

function obtenerOtpUsuario(int $usuarioId): ?array
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('SELECT * FROM usuario_otp WHERE usuario_id = ?');
    $stmt->execute([$usuarioId]);
    $fila = $stmt->fetch();
    return $fila ?: null;
}

function limpiarOtpUsuario(int $usuarioId): void
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare('DELETE FROM usuario_otp WHERE usuario_id = ?');
    $stmt->execute([$usuarioId]);
}

function validarCodigoOtp(int $usuarioId, string $codigo): array
{
    $otp = obtenerOtpUsuario($usuarioId);
    if (!$otp) {
        return ['ok' => false, 'error' => 'Código OTP no encontrado. Vuelva a iniciar sesión.'];
    }

    if (strtotime($otp['expiracion_en']) < time()) {
        limpiarOtpUsuario($usuarioId);
        return ['ok' => false, 'error' => 'El código OTP ha expirado. Vuelva a iniciar sesión.'];
    }

    if ($otp['codigo'] !== $codigo) {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('UPDATE usuario_otp SET intentos_fallidos = intentos_fallidos + 1, actualizado_en = NOW() WHERE usuario_id = ?');
        $stmt->execute([$usuarioId]);
        return ['ok' => false, 'error' => 'Código OTP incorrecto.'];
    }

    limpiarOtpUsuario($usuarioId);
    return ['ok' => true];
}

function renovarSesion(int $usuarioId, string $token): bool
{
    $usuario = usuarioPorId($usuarioId);
    if (!$usuario || ($usuario['sesion_activa_token'] ?? '') !== $token) {
        return false;
    }

    registrarSesionEnBd($usuarioId, $token, $usuario['rol_nombre'] ?? 'Otros');
    return true;
}

function cargarPermisosSesion(): array
{
    $permisos = $_SESSION['permisos'] ?? [];
    if (is_string($permisos)) {
        $permisos = json_decode($permisos, true) ?: [];
    }
    return is_array($permisos) ? $permisos : [];
}

function tienePermiso(string $modulo, string $accion): bool
{
    if (esAdministrador()) {
        return true;
    }

    $permisos = cargarPermisosSesion();
    return isset($permisos[$modulo][$accion]) && $permisos[$modulo][$accion] === true;
}

function requerirPermiso(string $modulo, string $accion): void
{
    requerirAutenticacion();
    if (!tienePermiso($modulo, $accion)) {
        header('Location: ' . BASE_PATH . '/public/index.php?error=sin_permiso');
        exit;
    }
}

function obtenerRutaLogoPdvsa(): ?string
{
    return obtenerConfiguracionSistema('ruta_logo_pdvsa', null);
}
