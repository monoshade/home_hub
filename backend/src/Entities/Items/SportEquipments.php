<?php

declare(strict_types=1);

namespace App\Entities\Items;

/**
 * A piece of sport equipment (bike, dumbbells, tennis racket, etc.).
 */
final class SportEquipments extends ItemBase
{
    public function __construct(
        ?int $id,
        string $name,
        public readonly ?string $sport = null,
        public readonly ?string $condition = null,     // e.g. new, used, worn
        public readonly ?string $purchaseDate = null,
        ?int $spaceId = null,
        ?string $createdAt = null,
    ) {
        parent::__construct($id, $name, $spaceId, $createdAt);
    }
}
