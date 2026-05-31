<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->routes['GET'][$path] = ['handler' => $handler, 'middleware' => $middleware];
    }

    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->routes['POST'][$path] = ['handler' => $handler, 'middleware' => $middleware];
    }

    public function match(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->routes['GET'][$path] = ['handler' => $handler, 'middleware' => $middleware];
        $this->routes['POST'][$path] = ['handler' => $handler, 'middleware' => $middleware];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $basePath = App::BASE_PATH;
        $path = substr($uri, strlen($basePath));
        $path = $path ?: '/';

        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $routePath => $route) {
            $pattern = $this->pathToPattern($routePath);
            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, fn($k) => !is_int($k), ARRAY_FILTER_USE_KEY);

                foreach ($route['middleware'] as $mw) {
                    call_user_func($mw);
                }

                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$class, $method] = $handler;
                    $controller = new $class();
                    call_user_func_array([$controller, $method], [$params]);
                } else {
                    call_user_func_array($handler, [$params]);
                }
                return;
            }
        }

        http_response_code(404);
        echo '404 - Página no encontrada';
    }

    private function pathToPattern(string $path): string
    {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
}
