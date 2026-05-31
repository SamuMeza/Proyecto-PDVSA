<?php
namespace App\Core;

class Logger
{
    private static string $logDir = '';

    public static function init(string $logDir = null): void
    {
        self::$logDir = $logDir ?: dirname(__DIR__, 2) . '/logs';
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
        }
    }

    public static function info(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::write('WARNING', $message, $context);
    }

    public static function auth(string $message, array $context = []): void
    {
        self::write('AUTH', $message, $context, 'auth.log');
    }

    public static function query(string $message, array $context = []): void
    {
        self::write('QUERY', $message, $context, 'queries.log');
    }

    private static function write(string $level, string $message, array $context = [], string $file = 'app.log'): void
    {
        if (empty(self::$logDir)) {
            self::init();
        }

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $line = "[{$timestamp}] {$level}: {$message}{$contextStr}" . PHP_EOL;

        $path = self::$logDir . '/' . $file;
        file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
    }
}
