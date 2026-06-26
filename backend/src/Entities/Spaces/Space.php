<?php

declare(strict_types=1);

namespace App\Entities\Spaces;

use App\Entities\EntityRowMapper;

/**
 * Common base for space-type entities (Room, House, Apartment, ...).
 * Holds the shared columns (id, name, area, description, created_at);
 * row hydration comes from EntityRowMapper.
 *
 * Each concrete space defines an explicit toArray() describing its formatted
 * API output, including the intrinsic `space_type` discriminator. The
 * relational `parent_space_id` (and any nested spaces/items) is added by the
 * controller / aggregate, not by the entity.
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

    /** The space type discriminator (e.g. "house", "room"). */
    abstract public function spaceType(): string;

    /** Formatted API representation (snake_case keys). */
    abstract public function toArray(): array;
}
