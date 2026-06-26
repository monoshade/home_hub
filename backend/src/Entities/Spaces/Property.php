<?php

declare(strict_types=1);

namespace App\Entities\Spaces;

/**
 * A real-estate property / parcel of land. Abstract base class for House and
 * Apartment, which supply the concrete space_type and formatted output.
 */
abstract class Property extends Space
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
