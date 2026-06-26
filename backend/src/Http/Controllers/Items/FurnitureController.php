<?php

declare(strict_types=1);

namespace App\Http\Controllers\Items;

use App\Entities\Items\Furniture;
use App\Http\Controllers\ResourceController;

/** REST API for furniture (/api/furniture). */
final class FurnitureController extends ResourceController
{
    public function basePath(): string
    {
        return '/api/furniture';
    }

    protected function table(): string
    {
        return 'furniture';
    }

    protected function writable(): array
    {
        return ['name', 'material', 'dimensions', 'purchase_date', 'space_id'];
    }

    protected function format(array $row): array
    {
        return Furniture::fromRow($row)->toArray();
    }
}
