<?php

declare(strict_types=1);

namespace App\Http;

/**
 * An incoming HTTP request: method, path, route vars, query string and
 * a decoded JSON body.
 */
final class Request
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public array $vars = [],
        private array $query = [],
        private ?array $body = null,
    ) {
    }

    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        parse_str($_SERVER['QUERY_STRING'] ?? '', $query);

        $body = null;
        $raw = file_get_contents('php://input');
        if ($raw !== false && $raw !== '') {
            $decoded = json_decode($raw, true);
            $body = is_array($decoded) ? $decoded : null;
        }

        return new self($method, $path, [], $query, $body);
    }

    public function query(string $key, ?string $default = null): ?string
    {
        return isset($this->query[$key]) ? (string) $this->query[$key] : $default;
    }

    public function body(): array
    {
        return $this->body ?? [];
    }
}
