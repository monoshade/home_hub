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

    public function spaceType(): string
    {
        return 'yard';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'space_type' => $this->spaceType(),
            'area' => $this->area,
            'description' => $this->description,
            'surface_type' => $this->surfaceType,
            'fenced' => $this->fenced,
            'created_at' => $this->createdAt,
        ];
    }
}
