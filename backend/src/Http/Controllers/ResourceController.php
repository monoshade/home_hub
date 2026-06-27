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
     * Query-string parameters accepted on the list endpoint. None by default;
     * resources with list filters (e.g. spaces) override this.
     *
     * @return string[]
     */
    protected function allowedQueryParams(): array
    {
        return [];
    }

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
        $this->validateQueryParams($req);

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
        $data = $req->body();
        $this->validateFields($data);

        return Response::json($this->format($this->repo->create($data)), 201);
    }

    protected function update(Request $req): array
    {
        $data = $req->body();
        $this->validateFields($data);

        $row = $this->repo->update((int) $req->vars['id'], $data);
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

    /**
     * Reject any query-string parameter not declared in allowedQueryParams()
     * with a 400, so undefined parameters fail loudly instead of being ignored.
     */
    protected function validateQueryParams(Request $req): void
    {
        $unknown = array_diff(array_keys($req->queryParams()), $this->allowedQueryParams());
        if ($unknown !== []) {
            throw HttpException::badRequest('Unknown query parameter(s): ' . implode(', ', $unknown));
        }
    }

    /**
     * Reject any body field not accepted for writing (see writable()) with a
     * 400, rather than silently dropping it on create/update.
     *
     * @param array<string, mixed> $data
     */
    protected function validateFields(array $data): void
    {
        $unknown = array_diff(array_keys($data), $this->writable());
        if ($unknown !== []) {
            throw HttpException::badRequest('Unknown field(s): ' . implode(', ', $unknown));
        }
    }
}
