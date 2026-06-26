<?php

declare(strict_types=1);

namespace App\Entities\Spaces;

/**
 * A standalone house. Can contain multiple rooms, yards, garages and decks.
 */
final class House extends Property
{
    /**
     * @param Room[]   $rooms
     * @param Yard[]   $yards
     * @param Garage[] $garages
     * @param Deck[]   $decks
     */
    public function __construct(
        ?int $id,
        string $name,
        public readonly ?int $floors = null,
        public readonly ?int $yearBuilt = null,
        public readonly array $rooms = [],
        public readonly array $yards = [],
        public readonly array $garages = [],
        public readonly array $decks = [],
        ?string $address = null,
        ?float $lotSize = null,
        ?string $propertyType = null,
        ?float $area = null,
        ?string $description = null,
        ?string $createdAt = null,
    ) {
        parent::__construct($id, $name, $address, $lotSize, $propertyType, $area, $description, $createdAt);
    }

    public function spaceType(): string
    {
        return 'house';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'space_type' => $this->spaceType(),
            'area' => $this->area,
            'description' => $this->description,
            'address' => $this->address,
            'lot_size' => $this->lotSize,
            'property_type' => $this->propertyType,
            'floors' => $this->floors,
            'year_built' => $this->yearBuilt,
            'created_at' => $this->createdAt,
        ];
    }
}
