<?php
namespace App\Core;

class Response
{
    public static function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    public static function notFound(): never
    {
        http_response_code(404);
        echo '404 - Página no encontrada';
        exit;
    }

    public static function forbidden(): never
    {
        http_response_code(403);
        echo '403 - Acceso denegado';
        exit;
    }

    public static function setHeader(string $name, string $value): void
    {
        header("{$name}: {$value}");
    }

    public static function setStatusCode(int $code): void
    {
        http_response_code($code);
    }
}
