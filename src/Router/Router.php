<?php

namespace App\Router;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    private function addRoute(
        string $method,
        string $path,
        callable $handler
    ): void {
        $this->routes[$method][$path] = $handler;
    }

    public function dispatch(
        string $method,
        string $path
    ): void {
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $paramNames = [];

            $pattern = preg_replace_callback(
                '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
                function (array $matches) use (&$paramNames): string {
                    $paramNames[] = $matches[1];

                    return '([^/]+)';
                },
                $route
            );

            $pattern = '#^' . $pattern . '$#';

            if (!preg_match($pattern, $path, $matches)) {
                continue;
            }

            array_shift($matches);

            $handler(...$matches);

            return;
        }

        http_response_code(404);
        echo '404 Not Found';
    }
}
