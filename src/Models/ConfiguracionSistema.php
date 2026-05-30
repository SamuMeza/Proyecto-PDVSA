<?php
namespace App\Models;

class ConfiguracionSistema extends Model
{
    protected static function table(): string { return 'configuracion_sistema'; }
    protected static function primaryKey(): string { return 'id'; }

    private static array $cache = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        try {
            $row = self::findFirstWhere('clave = ?', [$key]);
            if ($row) {
                self::$cache[$key] = $row['valor'];
                return self::$cache[$key];
            }
        } catch (\PDOException $e) {
            return $default;
        }

        return self::$cache[$key] = $default;
    }

    public static function getLogoPath(): ?string
    {
        return self::get('ruta_logo_pdvsa', null);
    }

    public static function getSessionMinutes(string $roleName): int
    {
        return match ($roleName) {
            'Administrador' => (int) self::get('sesion_minutos_admin', '10'),
            'Supervisor' => (int) self::get('sesion_minutos_supervisor', '20'),
            default => (int) self::get('sesion_minutos_otros', '35'),
        };
    }

    public static function getEmailDomain(): string
    {
        return self::get('email_dominio_interno', 'pdvsa.com');
    }

    public static function clearCache(): void
    {
        self::$cache = [];
    }
}
