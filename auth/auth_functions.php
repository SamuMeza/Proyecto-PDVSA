<?php
/**
 * Compatibilidad legacy — delega en src/ (App\*).
 * Mantiene las mismas funciones para no romper archivos existentes.
 */

require_once dirname(__DIR__) . '/config/autoload.php';
require_once dirname(__DIR__) . '/config/database.php';

use App\Core\App;
use App\Core\Session as CoreSession;
use App\Services\AuthService;
use App\Models\User;
use App\Models\Role;
use App\Models\UserOtp;
use App\Models\ConfiguracionSistema;
use App\Helpers\SecurityHelper;

define('BASE_PATH', App::BASE_PATH);
define('ROL_ADMINISTRADOR', AuthService::ROL_ADMIN);
define('OTP_TOKEN_MINUTES', AuthService::OTP_TOKEN_MINUTES);
define('OTP_GENERACION_LIMITE_DIARIO', AuthService::OTP_LIMITE_DIARIO);

function iniciarSesionPhp(): void { CoreSession::start(); }

function generarTokenSesion(): string { return CoreSession::generateToken(); }

function obtenerConfiguracionSistema(string $clave, $default = null) { return ConfiguracionSistema::get($clave, $default); }

function obtenerSegundosSesionPorRol(string $rolNombre): int { return CoreSession::timeoutForRole($rolNombre); }

function emailInternoParaUsuario(string $nombreUsuario): string
{
    return SecurityHelper::internalEmail($nombreUsuario, ConfiguracionSistema::getEmailDomain());
}

function normalizarTelefono58(string $telefono): ?string { return SecurityHelper::normalizePhoneVenezuela($telefono); }

function usuarioPorNombre(string $nombreUsuario): ?array { return User::findByUsernameWithRole($nombreUsuario) ?: null; }

function usuarioPorId(int $id): ?array { return User::findWithRole($id) ?: null; }

function registrarSesionEnBd(int $usuarioId, string $token, string $rolNombre): void
{
    User::registerSession($usuarioId, $token, $rolNombre);
}

function cerrarSesionEnBd(int $usuarioId): void { User::clearSession($usuarioId); }

function validarSesionBd(int $usuarioId, string $token): bool { return User::validateSession($usuarioId, $token); }

function iniciarSesionUsuario(array $usuario): void { AuthService::completeLogin($usuario); }

function cerrarSesionUsuario(): void { AuthService::logout(); }

function estaAutenticado(): bool { return AuthService::check(); }

function requerirAutenticacion(): void { AuthService::requireAuth(); }

function esAdministrador(): bool { return AuthService::isAdmin(); }

function requerirAdministrador(): void { AuthService::requireAdmin(); }

function rolesDisponibles(): array { return Role::allActive(); }

function crearUsuario(array $datos): array { return AuthService::createUser($datos); }

function intentarLogin(string $nombreUsuario, string $contrasena): array { return AuthService::login($nombreUsuario, $contrasena); }

function generarOtpParaUsuario(array $usuario): array { return AuthService::generateOtpForUser($usuario); }

function obtenerOtpUsuario(int $usuarioId): ?array { return UserOtp::findByUser($usuarioId) ?: null; }

function limpiarOtpUsuario(int $usuarioId): void { UserOtp::clear($usuarioId); }

function validarCodigoOtp(int $usuarioId, string $codigo): array { return AuthService::verifyOtp($usuarioId, $codigo); }

function renovarSesion(int $usuarioId, string $token): bool { return AuthService::renewSession($usuarioId, $token); }

function cargarPermisosSesion(): array { return AuthService::getPermissions(); }

function tienePermiso(string $modulo, string $accion): bool { return AuthService::hasPermission($modulo, $accion); }

function requerirPermiso(string $modulo, string $accion): void { AuthService::requirePermission($modulo, $accion); }

function obtenerRutaLogoPdvsa(): ?string { return ConfiguracionSistema::getLogoPath(); }
