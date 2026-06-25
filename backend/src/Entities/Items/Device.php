<?php

declare(strict_types=1);

namespace App\Entities\Items;

/**
 * An electronic device (TV, router, appliance, etc.).
 */
final class Device extends ItemBase
{
    public function __construct(
        ?int $id,
        string $name,
        public readonly ?string $brand = null,
        public readonly ?string $model = null,
        public readonly ?string $status = null,        // e.g. working, broken, retired
        public readonly ?string $purchaseDate = null,
        ?int $spaceId = null,
        ?string $createdAt = null,
    ) {
        parent::__construct($id, $name, $spaceId, $createdAt);
    }
}
