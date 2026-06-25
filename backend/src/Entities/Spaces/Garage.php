<?php

declare(strict_types=1);

namespace App\Entities\Spaces;

/**
 * A garage (attached or standalone).
 */
final class Garage extends Space
{
    public function __construct(
        ?int $id,
        string $name,
        public readonly ?int $capacity = null,         // number of vehicles
        public readonly ?bool $attached = null,
        ?float $area = null,
        ?string $description = null,
        ?string $createdAt = null,
    ) {
        parent::__construct($id, $name, $area, $description, $createdAt);
    }
}
