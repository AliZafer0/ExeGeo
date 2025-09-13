<?php
namespace App\Core;

final class Router {
    private array $routes = [];

    public function get(string $pattern, callable|array $handler): void {
        $this->add('GET', $pattern, $handler);
    }
    public function post(string $pattern, callable|array $handler): void {
        $this->add('POST', $pattern, $handler);
    }
    public function put(string $pattern, callable|array $handler): void {
        $this->add('PUT', $pattern, $handler);
    }
    public function delete(string $pattern, callable|array $handler): void {
        $this->add('DELETE', $pattern, $handler);
    }

    private function add(string $method, string $pattern, callable|array $handler): void {
        [$regex, $keys] = $this->compile($pattern);
        $this->routes[$method][] = [
            'pattern' => $pattern,
            'handler' => $handler,
            'regex'   => $regex,
            'keys'    => $keys
        ];
    }

    private function compile(string $pattern): array {
        $keys = [];
        $regex = preg_replace_callback('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', function($m) use (&$keys) {
            $keys[] = $m[1];
            return '(?P<' . $m[1] . '>[^/]+)';
        }, $pattern);
        return ['#^' . $regex . '$#', $keys];
    }

public function dispatch(string $method, string $uri): void {
    $path = parse_url($uri, PHP_URL_PATH) ?? '/';
    $allowed = [];

    foreach ($this->routes[$method] ?? [] as $r) {
        if (preg_match($r['regex'], $path, $m)) {
            $params = [];
            foreach ($r['keys'] as $k) {
                $params[$k] = $m[$k] ?? null;
            }
            // ÖNEMLİ: void fonksiyonda return ile değer döndürme yok
            $this->call($r['handler'], $params);
            return;
        }
    }

    // 405 mi 404 mü?
    foreach ($this->routes as $m => $list) {
        foreach ($list as $r) {
            if (preg_match($r['regex'], $path)) {
                $allowed[] = $m;
            }
        }
    }

    if ($allowed) {
        http_response_code(405);
        header('Allow: ' . implode(', ', array_unique($allowed)));
        echo '405 Method Not Allowed';
        return;
    }

    http_response_code(404);
    echo '404 Not Found';
}


    private function call(callable|array $handler, array $params): void {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $obj = new $class;
            echo $obj->{$method}(...array_values($params));
            return;
        }
        echo $handler(...array_values($params));
    }
}
