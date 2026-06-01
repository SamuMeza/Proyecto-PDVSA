<?php
namespace App\Services;

use App\Core\App;
use App\Core\Session;
use App\Models\User;
use App\Models\Role;
use App\Models\UserOtp;
use App\Models\ConfiguracionSistema;
use App\Helpers\SecurityHelper;

class AuthService
{
    public const ROL_ADMIN = 'Administrador';
    public const OTP_TOKEN_MINUTES = 5;
    public const OTP_LIMITE_DIARIO = 150;

    public static function login(string $username, string $password): array
    {
        $user = User::findByUsernameWithRole(trim($username));
        if (!$user) {
            return ['ok' => false, 'error' => 'Usuario o contraseña incorrectos.'];
        }

        if (!SecurityHelper::verifyPassword($password, $user['contrasena_hash'])) {
            return ['ok' => false, 'error' => 'Usuario o contraseña incorrectos.'];
        }

        $otpResult = self::generateOtp($user);
        if (!$otpResult['ok']) {
            return $otpResult;
        }

        Session::start();
        Session::set('pending_otp_user_id', (int) $user['id']);
        Session::set('pending_otp_usuario', $user['nombre_usuario']);
        Session::set('pending_otp_expires', time() + (self::OTP_TOKEN_MINUTES * 60));

        return ['ok' => true, 'needs_otp' => true];
    }

    public static function verifyOtp(int $userId, string $code): array
    {
        $otp = UserOtp::findByUser($userId);
        if (!$otp) {
            return ['ok' => false, 'error' => 'Código OTP no encontrado. Vuelva a iniciar sesión.'];
        }

        if (strtotime($otp['expiracion_en']) < time()) {
            UserOtp::clear($userId);
            return ['ok' => false, 'error' => 'El código OTP ha expirado. Vuelva a iniciar sesión.'];
        }

        if ($otp['codigo'] !== $code) {
            UserOtp::incrementFailedAttempts($userId);
            return ['ok' => false, 'error' => 'Código OTP incorrecto.'];
        }

        UserOtp::clear($userId);
        return ['ok' => true];
    }

    public static function completeLogin(array $user): void
    {
        Session::start();
        $token = Session::generateToken();
        User::registerSession((int) $user['id'], $token, $user['rol_nombre'] ?? 'Otros');

        Session::set('usuario_id', (int) $user['id']);
        Session::set('nombre_usuario', $user['nombre_usuario']);
        Session::set('nombre_completo', $user['nombre_completo']);
        Session::set('rol_id', (int) $user['rol_id']);
        Session::set('rol_nombre', $user['rol_nombre'] ?? '');
        Session::set('sesion_token', $token);
        Session::set('permisos', json_decode($user['permisos_json'] ?? '{}', true) ?: []);
    }

    public static function logout(): void
    {
        Session::start();
        if (Session::has('usuario_id') && Session::has('sesion_token')) {
            User::clearSession((int) Session::get('usuario_id'));
        }
        Session::destroy();
    }

    private static function logDir(): string
    {
        return dirname(__DIR__, 2) . '/logs';
    }

    public static function check(): bool
    {
        Session::start();
        if (!Session::has('usuario_id') || !Session::has('sesion_token')) {
            error_log(date('Y-m-d H:i:s') . ' [AuthService::check] No usuario_id o sesion_token en sesión. session_id=' . session_id() . PHP_EOL, 3, self::logDir() . '/app.log');
            return false;
        }
        $valid = User::validateSession((int) Session::get('usuario_id'), Session::get('sesion_token'));
        error_log(date('Y-m-d H:i:s') . ' [AuthService::check] uid=' . Session::get('usuario_id') . ' token=' . substr(Session::get('sesion_token') ?? '', 0, 8) . '... valid=' . ($valid ? 'true' : 'false') . ' session_id=' . session_id() . PHP_EOL, 3, self::logDir() . '/app.log');
        return $valid;
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: ' . App::BASE_PATH . '/auth/login.php');
            exit;
        }
    }

    public static function isAdmin(): bool
    {
        if (!self::check()) {
            error_log(date('Y-m-d H:i:s') . ' [AuthService::isAdmin] check() FAILED. session_id=' . session_id() . ' rol=' . Session::get('rol_nombre') . PHP_EOL, 3, self::logDir() . '/app.log');
            return false;
        }
        $rol = Session::get('rol_nombre');
        $result = $rol === self::ROL_ADMIN;
        error_log(date('Y-m-d H:i:s') . ' [AuthService::isAdmin] rol=' . var_export($rol, true) . ' ROL_ADMIN=' . self::ROL_ADMIN . ' result=' . ($result ? 'true' : 'false') . ' session_id=' . session_id() . PHP_EOL, 3, self::logDir() . '/app.log');
        return $result;
    }

    public static function requireAdmin(): void
    {
        self::requireAuth();
        if (!self::isAdmin()) {
            header('Location: ' . App::BASE_PATH . '/public/index.php?error=sin_permiso');
            exit;
        }
    }

    public static function hasPermission(string $module, string $action): bool
    {
        if (self::isAdmin()) return true;

        $permisos = self::getPermissions();
        return isset($permisos[$module][$action]) && $permisos[$module][$action] === true;
    }

    public static function requirePermission(string $module, string $action): void
    {
        self::requireAuth();
        if (!self::hasPermission($module, $action)) {
            header('Location: ' . App::BASE_PATH . '/public/index.php?error=sin_permiso');
            exit;
        }
    }

    public static function getPermissions(): array
    {
        $permisos = Session::get('permisos', []);
        if (is_string($permisos)) {
            $permisos = json_decode($permisos, true) ?: [];
        }
        return is_array($permisos) ? $permisos : [];
    }

    public static function renewSession(int $userId, string $token): bool
    {
        $user = User::findWithRole($userId);
        if (!$user || ($user['sesion_activa_token'] ?? '') !== $token) {
            return false;
        }
        User::registerSession($userId, $token, $user['rol_nombre'] ?? 'Otros');
        return true;
    }

    public static function createUser(array $data): array
    {
        $required = ['rol_id', 'nombre_completo', 'nombre_usuario', 'contrasena'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['ok' => false, 'error' => 'Complete todos los campos obligatorios.'];
            }
        }

        if (strlen($data['contrasena']) < 6) {
            return ['ok' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres.'];
        }

        $phone = $data['telefono_extension'] ?? '';
        if ($phone !== '') {
            $phone = SecurityHelper::normalizePhoneVenezuela($phone);
            if ($phone === null) {
                return ['ok' => false, 'error' => 'El teléfono debe comenzar con +58 y contener sólo números válidos.'];
            }
        }

        if (User::existsByUsername(trim($data['nombre_usuario']))) {
            return ['ok' => false, 'error' => 'El nombre de usuario ya está registrado.'];
        }

        if (!Role::existsActive((int) $data['rol_id'])) {
            return ['ok' => false, 'error' => 'Rol no válido.'];
        }

        $hash = SecurityHelper::hashPassword($data['contrasena']);
        $email = SecurityHelper::internalEmail(trim($data['nombre_usuario']));

        $id = User::insert([
            'rol_id' => (int) $data['rol_id'],
            'nombre_completo' => trim($data['nombre_completo']),
            'cargo' => $data['cargo'] ?? null,
            'email' => $email,
            'telefono_extension' => $phone,
            'nombre_usuario' => trim($data['nombre_usuario']),
            'contrasena_hash' => $hash,
            'estado' => 'activo',
            'creado_por' => !empty($data['creado_por']) ? (int) $data['creado_por'] : null,
        ]);

        return ['ok' => true, 'id' => $id];
    }

    public static function generateOtpForUser(array $user): array
    {
        return self::generateOtp($user);
    }

    private static function generateOtp(array $user): array
    {
        $userId = (int) $user['id'];
        $otp = UserOtp::findByUser($userId);
        $today = date('Y-m-d');
        $generatedToday = 0;

        if ($otp && $otp['fecha_ultimo_generado'] === $today) {
            $generatedToday = (int) $otp['generados_hoy'];
        }

        if ($generatedToday >= self::OTP_LIMITE_DIARIO) {
            return ['ok' => false, 'error' => 'Se alcanzó el límite diario de generación de códigos OTP.'];
        }

        $code = SecurityHelper::generateOtp();
        $expiration = date('Y-m-d H:i:s', time() + self::OTP_TOKEN_MINUTES * 60);
        $generatedToday++;

        if ($otp) {
            UserOtp::update($userId, [
                'codigo' => $code,
                'expiracion_en' => $expiration,
                'generados_hoy' => $generatedToday,
                'fecha_ultimo_generado' => $today,
                'intentos_fallidos' => 0,
                'actualizado_en' => date('Y-m-d H:i:s'),
            ]);
        } else {
            UserOtp::insert([
                'usuario_id' => $userId,
                'codigo' => $code,
                'expiracion_en' => $expiration,
                'generados_hoy' => $generatedToday,
                'fecha_ultimo_generado' => $today,
                'intentos_fallidos' => 0,
            ]);
        }

        return ['ok' => true, 'codigo' => $code];
    }
}
