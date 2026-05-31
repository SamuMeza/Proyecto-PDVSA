<?php
namespace App\Core;

class App
{
    public const NAME = 'Sistema PDVSA';
    public const BASE_PATH = '/sistema_pdvsa';

    private static ?App $instance = null;
    private ?Router $router = null;

    private function __construct()
    {
        $this->initEnv();
        $this->initTimezone();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function setRouter(Router $router): void
    {
        $this->router = $router;
    }

    public function getRouter(): ?Router
    {
        return $this->router;
    }

    public function run(): void
    {
        if ($this->router) {
            $this->router->dispatch();
        }
    }

    private function initEnv(): void
    {
        $envFile = dirname(__DIR__, 2) . '/.env';
        if (is_file($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) continue;
                $pos = strpos($line, '=');
                if ($pos !== false) {
                    $key = trim(substr($line, 0, $pos));
                    $val = trim(substr($line, $pos + 1));
                    $val = trim($val, '"\'');
                    putenv("$key=$val");
                    $_ENV[$key] = $val;
                }
            }
        }
    }

    private function initTimezone(): void
    {
        $tz = getenv('APP_TIMEZONE') ?: 'America/Caracas';
        date_default_timezone_set($tz);
    }
}
