<?php

declare(strict_types=1);

namespace App\Entities\Spaces;

use App\Entities\EntityRowMapper;

/**
 * Common base for space-type entities (Room, House, Apartment, ...).
 * Holds the shared columns (id, name, area, description, created_at);
 * row hydration and serialization come from EntityRowMapper.
 */
abstract class Space
{
    use EntityRowMapper;

    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?float $area = null,           // floor area, in your unit of choice
        public readonly ?string $description = null,
        public readonly ?string $createdAt = null,
    ) {
    }
}
