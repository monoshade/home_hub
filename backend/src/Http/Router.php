<?php

declare(strict_types=1);

namespace App\Http;

/**
 * A tiny method + path router. Patterns may contain {name} placeholders,
 * captured into Request::$vars.
 */
final class Router
{
    /** @var array<string, array<int, array{0: string, 1: callable}>> */
    private array $routes = [];

    public function get(string $pattern, callable $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    public function put(string $pattern, callable $handler): void
    {
        $this->add('PUT', $pattern, $handler);
    }

    public function delete(string $pattern, callable $handler): void
    {
        $this->add('DELETE', $pattern, $handler);
    }

    public function add(string $method, string $pattern, callable $handler): void
    {
        $regex = '#^' . preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
        $this->routes[$method][] = [$regex, $handler];
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes[$request->method] ?? [] as [$regex, $handler]) {
            if (preg_match($regex, $request->path, $matches)) {
                $request->vars = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $result = $handler($request);
                return $result instanceof Response ? $result : Response::json($result);
            }
        }

        throw HttpException::notFound("No route for {$request->method} {$request->path}");
    }
}
