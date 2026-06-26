<?php

declare(strict_types=1);

namespace App\Entities\Items;

use App\Entities\EntityRowMapper;

/**
 * Common base for belonging-type entities (Device, Furniture, ...).
 * Holds the shared columns (id, name, space_id, created_at); row hydration
 * comes from EntityRowMapper.
 *
 * Every item lives in at most one location: $spaceId is a foreign key to a
 * single spaces row (see db schema). This replaces the old free-text
 * location/room fields.
 *
 * Each concrete item defines an explicit toArray() describing its formatted
 * API output, including the intrinsic `category` discriminator.
 */
abstract class ItemBase
{
    use EntityRowMapper;

    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?int $spaceId = null,
        public readonly ?string $createdAt = null,
    ) {
    }

    /** The item category, matching the resource path (e.g. "sport-equipments"). */
    abstract public function category(): string;

    /** Formatted API representation (snake_case keys). */
    abstract public function toArray(): array;
}
