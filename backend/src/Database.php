<?php

declare(strict_types=1);

namespace App;

use PDO;

/**
 * Thin PDO factory. Reads connection details from environment variables
 * (set in docker-compose.yml). The {@see Db} from the runtime {@see Context}
 * selects which database to connect to.
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(Db $db = Db::Prod): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $host = getenv('DB_HOST') ?: 'localhost';
        $port = getenv('DB_PORT') ?: '5432';
        $name = self::databaseName($db);
        $user = getenv('DB_USER') ?: 'home_hub';
        $pass = getenv('DB_PASSWORD') ?: 'home_hub';

        $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $name);

        self::$pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return self::$pdo;
    }

    /**
     * Resolve the database name for a given context. Each case may override its
     * name via a dedicated env var (DB_NAME_PROD / DB_NAME_DEMO / DB_NAME_TEST),
     * otherwise falling back to the shared DB_NAME (default: demo).
     */
    private static function databaseName(Db $db): string
    {
        $base = getenv('DB_NAME') ?: 'demo';

        return getenv('DB_NAME_' . strtoupper($db->value)) ?: $base;
    }
}
