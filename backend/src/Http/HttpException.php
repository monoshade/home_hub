<?php

declare(strict_types=1);

namespace App\Http;

use RuntimeException;

/**
 * An exception carrying an HTTP status code, mapped to a response by index.php.
 */
final class HttpException extends RuntimeException
{
    public function __construct(public readonly int $status, string $message)
    {
        parent::__construct($message);
    }

    public static function notFound(string $message = 'Not found'): self
    {
        return new self(404, $message);
    }

    public static function badRequest(string $message = 'Bad request'): self
    {
        return new self(400, $message);
    }
}
