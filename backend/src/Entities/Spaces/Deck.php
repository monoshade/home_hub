<?php

declare(strict_types=1);

namespace App\Entities\Spaces;

/**
 * An outdoor deck or patio.
 */
final class Deck extends Space
{
    public function __construct(
        ?int $id,
        string $name,
        public readonly ?string $material = null,      // e.g. wood, composite, concrete
        public readonly ?bool $covered = null,
        ?float $area = null,
        ?string $description = null,
        ?string $createdAt = null,
    ) {
        parent::__construct($id, $name, $area, $description, $createdAt);
    }
}
