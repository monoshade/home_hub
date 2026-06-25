<?php

declare(strict_types=1);

namespace App\Entities\Spaces;

/**
 * A real-estate property / parcel of land. Base class for House and Apartment.
 */
class Property extends Space
{
    public function __construct(
        ?int $id,
        string $name,
        public readonly ?string $address = null,
        public readonly ?float $lotSize = null,
        public readonly ?string $propertyType = null,  // e.g. residential, commercial, land
        ?float $area = null,
        ?string $description = null,
        ?string $createdAt = null,
    ) {
        parent::__construct($id, $name, $area, $description, $createdAt);
    }
}
