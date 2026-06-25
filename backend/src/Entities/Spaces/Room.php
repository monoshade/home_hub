<?php

declare(strict_types=1);

namespace App\Entities\Spaces;

/**
 * A room within a building (bedroom, kitchen, bathroom, etc.).
 */
final class Room extends Space
{
    public function __construct(
        ?int $id,
        string $name,
        public readonly ?string $type = null,          // e.g. bedroom, kitchen, bathroom
        public readonly ?int $floorLevel = null,
        ?float $area = null,
        ?string $description = null,
        ?string $createdAt = null,
    ) {
        parent::__construct($id, $name, $area, $description, $createdAt);
    }
}
