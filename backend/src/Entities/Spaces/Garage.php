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

    public function spaceType(): string
    {
        return 'garage';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'space_type' => $this->spaceType(),
            'area' => $this->area,
            'description' => $this->description,
            'capacity' => $this->capacity,
            'attached' => $this->attached,
            'created_at' => $this->createdAt,
        ];
    }
}
