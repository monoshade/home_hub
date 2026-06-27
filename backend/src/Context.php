<?php

declare(strict_types=1);

namespace App;

/**
 * Runtime context for a single boot of the application: which database we talk
 * to and which environment we run as. Built once at the entry point
 * ({@see backend/public/index.php}) and threaded through the app.
 *
 * The enum cases live in their own files so they autoload independently:
 *   - {@see Db}          prod | demo | test
 *   - {@see Environment} prod | demo | test
 */
final class Context
{
    public function __construct(
        public readonly Db $db,
        public readonly Environment $environment,
    ) {
    }

    /**
     * Build the context from the parameters supplied at startup (environment
     * variables; set in docker-compose.yml / .env):
     *
     *   APP_DB  -> prod | demo | test  (default: demo)
     *   APP_ENV -> prod | demo | test  (default: demo)
     *
     * Unknown values fall back to the safe demo defaults.
     */
    public static function fromEnv(): self
    {
        $db = Db::tryFrom((string) (getenv('APP_DB') ?: '')) ?? Db::Demo;
        $environment = Environment::tryFrom((string) (getenv('APP_ENV') ?: '')) ?? Environment::Demo;

        return new self($db, $environment);
    }

    /** Plain-array view, handy for the /api/context and /api/health responses. */
    public function toArray(): array
    {
        return [
            'db' => $this->db->value,
            'environment' => $this->environment->value,
        ];
    }
}
