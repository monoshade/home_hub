<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\Repository\Repository;
use PDO;

/**
 * Base class for a per-entity REST resource. Owns the CRUD route wiring and the
 * default handlers; each concrete controller declares its own path, table,
 * writable columns and — crucially — the formatted return value via format().
 *
 * Routes registered for base path `/api/<resource>`:
 *   GET    /api/<resource>          list
 *   GET    /api/<resource>/{id}     read
 *   POST   /api/<resource>          create
 *   PUT    /api/<resource>/{id}     update
 *   DELETE /api/<resource>/{id}     delete
 */
abstract class ResourceController
{
    protected Repository $repo;

    public function __construct(PDO $pdo)
    {
        $this->repo = new Repository($pdo, $this->table(), $this->writable());
    }

    /** Base route path, e.g. "/api/devices". */
    abstract public function basePath(): string;

    /** Backing table name. */
    abstract protected function table(): string;

    /** Columns accepted for create/update. */
    abstract protected function writable(): array;

    /**
     * Map a raw database row to this resource's formatted response shape.
     * This is the single place each resource defines its return value.
     *
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    abstract protected function format(array $row): array;

    public function register(Router $router): void
    {
        $base = $this->basePath();
        $router->get($base, fn (Request $req) => $this->index($req));
        $router->get("$base/{id}", fn (Request $req) => $this->show($req));
        $router->post($base, fn (Request $req) => $this->store($req));
        $router->put("$base/{id}", fn (Request $req) => $this->update($req));
        $router->delete("$base/{id}", fn (Request $req) => $this->destroy($req));
    }

    /**
     * Formatted list of every row. Exposed publicly so aggregate endpoints
     * (e.g. /api/items, /api/properties) can compose resources.
     */
    public function all(): array
    {
        return array_map(fn (array $row) => $this->format($row), $this->repo->all());
    }

    protected function index(Request $req): array
    {
        return $this->all();
    }

    protected function show(Request $req): array
    {
        $row = $this->repo->find((int) $req->vars['id']);
        if ($row === null) {
            throw HttpException::notFound();
        }

        return $this->format($row);
    }

    protected function store(Request $req): Response
    {
        return Response::json($this->format($this->repo->create($req->body())), 201);
    }

    protected function update(Request $req): array
    {
        $row = $this->repo->update((int) $req->vars['id'], $req->body());
        if ($row === null) {
            throw HttpException::notFound();
        }

        return $this->format($row);
    }

    protected function destroy(Request $req): Response
    {
        if (!$this->repo->delete((int) $req->vars['id'])) {
            throw HttpException::notFound();
        }

        return new Response(null, 204);
    }
}
