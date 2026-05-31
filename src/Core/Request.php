<?php
namespace App\Core;

class Request
{
    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function isGet(): bool
    {
        return self::method() === 'GET';
    }

    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public static function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    public static function all(): array
    {
        return $_REQUEST;
    }

    public static function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_REQUEST[$key]);
    }

    public static function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    public static function redirectWithError(string $url, string $error): never
    {
        $separator = str_contains($url, '?') ? '&' : '?';
        header('Location: ' . $url . $separator . 'error=' . urlencode($error));
        exit;
    }

    public static function uri(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
}
