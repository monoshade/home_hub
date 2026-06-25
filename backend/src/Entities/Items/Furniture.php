<?php

declare(strict_types=1);

namespace App\Entities\Items;

/**
 * A piece of furniture (sofa, desk, shelf, etc.).
 */
final class Furniture extends ItemBase
{
    public function __construct(
        ?int $id,
        string $name,
        public readonly ?string $material = null,
        public readonly ?string $dimensions = null,    // e.g. "200x90x75 cm"
        public readonly ?string $purchaseDate = null,
        ?int $spaceId = null,
        ?string $createdAt = null,
    ) {
        parent::__construct($id, $name, $spaceId, $createdAt);
    }
}
