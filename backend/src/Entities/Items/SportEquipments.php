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

    public function category(): string
    {
        return 'sport-equipments';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sport' => $this->sport,
            'condition' => $this->condition,
            'purchase_date' => $this->purchaseDate,
            'space_id' => $this->spaceId,
            'category' => $this->category(),
            'created_at' => $this->createdAt,
        ];
    }
}
