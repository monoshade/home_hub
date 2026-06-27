<?php

declare(strict_types=1);

use App\Context;
use App\Database;
use App\Db;
use App\Environment;
use App\Http\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use PHPUnit\Framework\TestCase;

// create_router() is a plain function defined in routes.php; it is not covered
// by the App\ PSR-4 autoloader, so pull it in explicitly (idempotent).
require_once __DIR__ . '/../../src/routes.php';

/**
 * Shared helpers for the Home Hub backend suite:
 *   - pdo():           a direct, UN-cached PDO to any context database, used for
 *                      snapshotting (never goes through App\Database's static cache).
 *   - appRouter():     the real Router the app builds, using App\Database's OWN
 *                      connection resolver — so the solution's context handling
 *                      is what's exercised.
 *   - call():          dispatch a Request in-process, mapping HttpException to a
 *                      JSON error Response exactly like public/index.php.
 *   - schemaSnapshot()/dataSnapshot(): structure + rows, for the isolation tests.
 */
abstract class ApiTestCase extends TestCase
{
    /** Direct PDO to a literal database name (bypasses App\Database's static cache). */
    protected function pdo(string $database): PDO
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '5432';
        $user = getenv('DB_USER') ?: 'home_hub';
        $pass = getenv('DB_PASSWORD') ?: 'home_hub';

        return new PDO(
            "pgsql:host={$host};port={$port};dbname={$database}",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ],
        );
    }

    /**
     * The application's router for a given context, built through App\Database so
     * the solution's own DB-selection logic is under test.
     */
    protected function appRouter(Db $db, Environment $env = Environment::Demo): Router
    {
        return create_router(Database::connection($db), new Context($db, $env));
    }

    /**
     * Dispatch a request through the router, mirroring index.php's mapping of
     * HttpException -> JSON error Response so tests can assert on status codes.
     */
    protected function call(
        Router $router,
        string $method,
        string $path,
        array $query = [],
        ?array $body = null,
    ): Response {
        $path = rtrim($path, '/') ?: '/';
        try {
            return $router->dispatch(new Request($method, $path, [], $query, $body));
        } catch (HttpException $e) {
            return Response::json(['error' => $e->getMessage()], $e->status);
        }
    }

    /** Names of all public base tables, in a stable order. */
    protected function tables(PDO $pdo): array
    {
        $sql = "SELECT table_name FROM information_schema.tables
                WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
                ORDER BY table_name";

        return $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }

    /** Column-level schema snapshot for the whole public schema (or one table). */
    protected function schemaSnapshot(PDO $pdo, ?string $table = null): array
    {
        $sql = "SELECT table_name, column_name, data_type, is_nullable, column_default
                FROM information_schema.columns
                WHERE table_schema = 'public'";
        $params = [];
        if ($table !== null) {
            $sql .= " AND table_name = :t";
            $params['t'] = $table;
        }
        $sql .= " ORDER BY table_name, ordinal_position";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /** Row snapshot of every public table (or a given subset), ordered by id. */
    protected function dataSnapshot(PDO $pdo, ?array $tables = null): array
    {
        $tables ??= $this->tables($pdo);
        $snapshot = [];
        foreach ($tables as $table) {
            $snapshot[$table] = $pdo->query("SELECT * FROM {$table} ORDER BY id")->fetchAll();
        }

        return $snapshot;
    }
}
