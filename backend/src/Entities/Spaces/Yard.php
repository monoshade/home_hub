<?php

declare(strict_types=1);

namespace App\Entities\Spaces;

/**
 * An outdoor yard or garden area.
 */
final class Yard extends Space
{
    public function __construct(
        ?int $id,
        string $name,
        public readonly ?string $surfaceType = null,   // e.g. grass, gravel, concrete
        public readonly ?bool $fenced = null,
        ?float $area = null,
        ?string $description = null,
        ?string $createdAt = null,
    ) {
        parent::__construct($id, $name, $area, $description, $createdAt);
    }
}
