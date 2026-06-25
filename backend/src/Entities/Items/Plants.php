<?php

declare(strict_types=1);

namespace App\Entities\Items;

/**
 * A plant (houseplant, garden plant, etc.).
 */
final class Plants extends ItemBase
{
    public function __construct(
        ?int $id,
        string $name,
        public readonly ?string $species = null,
        public readonly ?int $wateringFrequencyDays = null,
        public readonly ?string $lastWatered = null,
        ?int $spaceId = null,
        ?string $createdAt = null,
    ) {
        parent::__construct($id, $name, $spaceId, $createdAt);
    }
}
