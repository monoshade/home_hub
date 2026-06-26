<?php

declare(strict_types=1);

namespace App\Entities;

use ReflectionClass;
use ReflectionNamedType;

/**
 * Row hydration shared by entity base classes: builds a typed entity from a
 * database row.
 *
 * Mapping convention: a camelCase constructor parameter maps to the
 * snake_case database column of the same name (e.g. floorLevel <-> floor_level).
 *
 * Note: this only handles the input side (row -> entity). Each concrete entity
 * defines its own explicit toArray() for the formatted API output, so the
 * response shape is an intentional contract rather than a reflection of the
 * object's internals.
 */
trait EntityRowMapper
{
    /**
     * Build an entity from a database row, mapping snake_case columns to
     * the class's constructor parameters and casting scalar types.
     */
    public static function fromRow(array $row): static
    {
        $ref = new ReflectionClass(static::class);

        $args = [];
        foreach ($ref->getConstructor()?->getParameters() ?? [] as $param) {
            $column = self::toSnakeCase($param->getName());

            // No matching column (e.g. relation properties): fall back to the
            // parameter's default so we don't pass null into a non-nullable type.
            if (!array_key_exists($column, $row)) {
                $args[] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;
                continue;
            }

            $value = $row[$column];

            if ($value !== null) {
                $type = $param->getType();
                if ($type instanceof ReflectionNamedType) {
                    $value = match ($type->getName()) {
                        'int' => (int) $value,
                        'float' => (float) $value,
                        'bool' => self::toBool($value),
                        'string' => (string) $value,
                        default => $value,
                    };
                }
            }

            $args[] = $value;
        }

        return $ref->newInstanceArgs($args);
    }

    private static function toSnakeCase(string $name): string
    {
        return strtolower((string) preg_replace('/[A-Z]/', '_$0', $name));
    }

    /**
     * Postgres returns booleans as 't'/'f' strings, so handle those
     * explicitly rather than relying on a plain (bool) cast.
     */
    private static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower((string) $value), ['t', 'true', '1', 'yes', 'on'], true);
    }
}
