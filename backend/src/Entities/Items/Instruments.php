<?php

declare(strict_types=1);

namespace App\Entities\Items;

/**
 * A musical instrument (guitar, piano, violin, etc.).
 */
final class Instruments extends ItemBase
{
    public function __construct(
        ?int $id,
        string $name,
        public readonly ?string $type = null,          // e.g. string, percussion, wind
        public readonly ?string $brand = null,
        public readonly ?string $purchaseDate = null,
        ?int $spaceId = null,
        ?string $createdAt = null,
    ) {
        parent::__construct($id, $name, $spaceId, $createdAt);
    }
}
