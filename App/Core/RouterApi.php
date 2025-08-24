<?php
declare(strict_types=1);

namespace App\Core;

class RouterAPI
{
    private array $routes = [
        'GET'    => [],
        'POST'   => [],
        'PUT'    => [],
        'PATCH'  => [],
        'DELETE' => [],
    ];

    public function get(string $pattern, callable|array $handler, bool $protected = false): void { $this->add('GET', $pattern, $handler, $protected); }
    public function post(string $pattern, callable|array $handler, bool $protected = false): void { $this->add('POST', $pattern, $handler, $protected); }
    public function put(string $pattern, callable|array $handler, bool $protected = false): void { $this->add('PUT', $pattern, $handler, $protected); }
    public function patch(string $pattern, callable|array $handler, bool $protected = false): void { $this->add('PATCH', $pattern, $handler, $protected); }
    public function delete(string $pattern, callable|array $handler, bool $protected = false): void { $this->add('DELETE', $pattern, $handler, $protected); }

    private function add(string $method, string $pattern, callable|array $handler, bool $protected): void
    {
        $regex = $this->compilePattern($pattern);
        $this->routes[$method][] = [
            'pattern' => $pattern,
            'regex' => $regex,
            'handler' => $handler,
            'protected' => $protected,
        ];
    }

    private function compilePattern(string $pattern): string
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);
        return '#^' . rtrim($regex, '/') . '$#';
    }

    public function dispatch(string $method, string $uri): bool
    {
        header('Content-Type: application/json; charset=utf-8');

        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['regex'], $uri, $matches)) {
                if ($route['protected'] && (!isset($_SESSION['user_id']))) {
                    http_response_code(401);
                    echo json_encode(['status' => 'error', 'message' => 'Unauthorized: يجب تسجيل الدخول']);
                    return true; // Route موجود لكن غير مصرح
                }

                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $handler = $route['handler'];

                try {
                    if (is_array($handler)) {
                        [$class, $action] = $handler;
                        $controller = new $class();
                        $result = $controller->$action(...array_values($params));
                    } else {
                        $result = $handler(...array_values($params));
                    }

                    http_response_code(200);
                    echo json_encode(['status' => 'success', 'data' => $result]);
                    return true;
                } catch (\Throwable $e) {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Server Error', 'error' => $e->getMessage()]);
                    return true;
                }
            }
        }

        return false; // لم نجد route
    }
}
