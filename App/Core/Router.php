<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [
        'GET'    => [],
        'POST'   => [],
        'PUT'    => [],
        'PATCH'  => [],
        'DELETE' => [],
    ];
    public function get(string $pattern, callable|array $handler, bool $protected = false): void
    {
        $this->add('GET', $pattern, $handler, $protected);
    }
    public function post(string $pattern, callable|array $handler, bool $protected = false): void
    {
        $this->add('POST', $pattern, $handler, $protected);
    }
    public function put(string $pattern, callable|array $handler, bool $protected = false): void
    {
        $this->add('PUT', $pattern, $handler, $protected);
    }
    public function patch(string $pattern, callable|array $handler, bool $protected = false): void
    {
        $this->add('PATCH', $pattern, $handler, $protected);
    }
    public function delete(string $pattern, callable|array $handler, bool $protected = false): void
    {
        $this->add('DELETE', $pattern, $handler, $protected);
    }

    private function add(string $method, string $pattern, callable|array $handler, bool $protected): void
    {
        $regex = $this->compilePattern($pattern);
        $this->routes[$method][] = [
            'pattern'   => $pattern,
            'regex'     => $regex,
            'handler'   => $handler,
            'protected' => $protected,
        ];
    }

    private function compilePattern(string $pattern): string
    {
      
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $regex . '$#';
    }

 
    public function dispatch(string $method, string $uri): void
    {
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['regex'], $uri, $matches)) {

                
                if ($route['protected'] && !Auth::check()) {
                    (new Controller())->redirect('/login');
                }

                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                $handler = $route['handler'];

                if (is_array($handler)) {
                    [$class, $method] = $handler;
                    $controller = new $class();
                    $controller->$method(...array_values($params));
                    return;
                }

               
                $handler(...array_values($params));
                return;
            }
        }

      
        http_response_code(404);
        echo "Route not found: {$method} {$uri}";
    }
}
