<?php

declare(strict_types=1);

namespace App\Entities;

use ReflectionClass;
use ReflectionNamedType;

/**
 * Generic row hydration / serialization shared by entity base classes.
 *
 * Mapping convention: a camelCase constructor parameter maps to the
 * snake_case database column of the same name (e.g. floorLevel <-> floor_level).
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

    /**
     * Plain array for JSON responses, with snake_case keys. Nested entities
     * and collections of entities are serialized recursively.
     */
    public function toArray(): array
    {
        $out = [];
        foreach (get_object_vars($this) as $property => $value) {
            $out[self::toSnakeCase($property)] = self::serializeValue($value);
        }

        return $out;
    }

    private static function serializeValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(static fn ($item) => self::serializeValue($item), $value);
        }

        if (is_object($value) && method_exists($value, 'toArray')) {
            return $value->toArray();
        }

        return $value;
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
