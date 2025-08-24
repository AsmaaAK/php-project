<?php
declare(strict_types=1);

namespace App\Core;

class Controller
{
    protected function render(string $view, array $data = [], ?string $layout = 'layout'): void
    {
        extract($data, EXTR_SKIP);
        $viewPath = __DIR__ . "/../Views/{$view}.php";
        if (!file_exists($viewPath)) {
            http_response_code(404);
            echo "View not found: {$view}";
            return;
        }

        if ($layout) {
            $layoutPath = __DIR__ . "/../Views/{$layout}.php";
            ob_start();
            require $viewPath;
            $content = ob_get_clean();

            require $layoutPath;
        } else {
            require $viewPath;
        }
    }

    protected function redirect(string $path): void
    {
        $config = require __DIR__ .'/config.php';
        $base=$this->config['app']['base_url'] ?? '/';
        $base   = rtrim($base, '/');
        header('Location: ' . $base . $path);
        exit;
    }

            protected function json($data, int $statusCode = 200) {
        // Don't remove headers - preserve CORS headers set by middleware
        header("Content-Type: application/json; charset=UTF-8");
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}
