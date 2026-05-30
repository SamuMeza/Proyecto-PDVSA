<?php
namespace App\Helpers;

class SecurityHelper
{
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    public static function generateOtp(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function generateCode(string $prefix = ''): string
    {
        return $prefix . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
    }

    public static function normalizePhoneVenezuela(string $phone): ?string
    {
        $phone = trim($phone);
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        if (!str_starts_with($phone, '+58')) {
            $phone = '+58' . ltrim($phone, '0');
        }
        if (!preg_match('/^\+58[0-9]{7,14}$/', $phone)) {
            return null;
        }
        return $phone;
    }

    public static function internalEmail(string $username, string $domain = 'pdvsa.com'): string
    {
        $clean = strtolower(trim($username));
        $clean = preg_replace('/[^a-z0-9_.-]/', '', $clean);
        return $clean . '@' . $domain;
    }
}
