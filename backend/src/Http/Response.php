<?php

declare(strict_types=1);

namespace App\Http;

/**
 * A JSON response with a status code.
 */
final class Response
{
    public function __construct(
        public readonly mixed $data,
        public readonly int $status = 200,
    ) {
    }

    public static function json(mixed $data, int $status = 200): self
    {
        return new self($data, $status);
    }

    public function send(): void
    {
        http_response_code($this->status);
        if ($this->status === 204) {
            return;
        }
        echo json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
