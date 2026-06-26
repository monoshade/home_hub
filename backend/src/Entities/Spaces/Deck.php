<?php

declare(strict_types=1);

namespace App\Entities\Spaces;

/**
 * An outdoor deck or patio.
 */
final class Deck extends Space
{
    public function __construct(
        ?int $id,
        string $name,
        public readonly ?string $material = null,      // e.g. wood, composite, concrete
        public readonly ?bool $covered = null,
        ?float $area = null,
        ?string $description = null,
        ?string $createdAt = null,
    ) {
        parent::__construct($id, $name, $area, $description, $createdAt);
    }

    public function spaceType(): string
    {
        return 'deck';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'space_type' => $this->spaceType(),
            'area' => $this->area,
            'description' => $this->description,
            'material' => $this->material,
            'covered' => $this->covered,
            'created_at' => $this->createdAt,
        ];
    }
}
