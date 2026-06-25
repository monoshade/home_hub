<?php

declare(strict_types=1);

namespace App\Entities\Items;

/**
 * A vehicle (car, bike, scooter, etc.).
 */
final class Vehicles extends ItemBase
{
    public function __construct(
        ?int $id,
        string $name,
        public readonly ?string $type = null,          // e.g. car, motorcycle, bicycle
        public readonly ?string $make = null,
        public readonly ?string $model = null,
        public readonly ?int $year = null,
        public readonly ?string $licensePlate = null,
        ?int $spaceId = null,
        ?string $createdAt = null,
    ) {
        parent::__construct($id, $name, $spaceId, $createdAt);
    }
}
