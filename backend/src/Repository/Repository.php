<?php

declare(strict_types=1);

namespace App\Repository;

use Closure;
use InvalidArgumentException;
use PDO;

/**
 * Generic CRUD repository for a single table.
 *
 * Writes are restricted to the configured $writable columns (a whitelist), and
 * all values are bound — request keys never reach SQL directly. Rows are passed
 * through the $serialize closure on the way out (typically Entity::fromRow()->toArray()).
 */
final class Repository
{
    /**
     * @param string[]                  $writable  columns accepted for insert/update/filter
     * @param Closure(array): array     $serialize row -> output array
     */
    public function __construct(
        private PDO $pdo,
        private string $table,
        private array $writable,
        private Closure $serialize,
    ) {
    }

    /** @param array<string, mixed> $filters */
    public function all(array $filters = []): array
    {
        $columns = array_values(array_intersect($this->writable, array_keys($filters)));
        $where = '';
        $params = [];
        if ($columns) {
            $clauses = [];
            foreach ($columns as $column) {
                $clauses[] = "$column = :$column";
                $params[$column] = $filters[$column];
            }
            $where = ' WHERE ' . implode(' AND ', $clauses);
        }

        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table}{$where} ORDER BY id");
        $stmt->execute($this->normalize($params));

        return array_map($this->serialize, $stmt->fetchAll());
    }

    public function find(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? ($this->serialize)($row) : null;
    }

    /** @param array<string, mixed> $data */
    public function create(array $data): array
    {
        $columns = array_values(array_intersect($this->writable, array_keys($data)));
        if (!$columns) {
            throw new InvalidArgumentException('No writable fields provided');
        }

        $names = implode(', ', $columns);
        $binds = implode(', ', array_map(static fn ($c) => ":$c", $columns));
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} ($names) VALUES ($binds) RETURNING *");
        $stmt->execute($this->normalize($this->pick($columns, $data)));

        return ($this->serialize)($stmt->fetch());
    }

    /** @param array<string, mixed> $data */
    public function update(int $id, array $data): ?array
    {
        $columns = array_values(array_intersect($this->writable, array_keys($data)));
        if (!$columns) {
            return $this->find($id);
        }

        $set = implode(', ', array_map(static fn ($c) => "$c = :$c", $columns));
        $params = $this->pick($columns, $data);
        $params['id'] = $id;

        $stmt = $this->pdo->prepare("UPDATE {$this->table} SET $set WHERE id = :id RETURNING *");
        $stmt->execute($this->normalize($params));
        $row = $stmt->fetch();

        return $row ? ($this->serialize)($row) : null;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    /** @param string[] $columns */
    private function pick(array $columns, array $data): array
    {
        $picked = [];
        foreach ($columns as $column) {
            $picked[$column] = $data[$column];
        }

        return $picked;
    }

    /** Make PHP values safe for PDO binding (booleans -> Postgres-friendly text). */
    private function normalize(array $params): array
    {
        foreach ($params as $key => $value) {
            if (is_bool($value)) {
                $params[$key] = $value ? 'true' : 'false';
            }
        }

        return $params;
    }
}
