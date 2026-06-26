<?php

declare(strict_types=1);

namespace App\Entities\Spaces;

/**
 * An apartment unit within a building. Can contain multiple rooms and one garage.
 */
final class Apartment extends Property
{
    /**
     * @param Room[] $rooms
     */
    public function __construct(
        ?int $id,
        string $name,
        public readonly ?string $unitNumber = null,
        public readonly ?int $floorLevel = null,
        public readonly array $rooms = [],
        public readonly ?Garage $garage = null,
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
        return 'apartment';
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
            'unit_number' => $this->unitNumber,
            'floor_level' => $this->floorLevel,
            'created_at' => $this->createdAt,
        ];
    }
}
