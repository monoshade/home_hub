<?php

declare(strict_types=1);

namespace App\Http\Controllers\Items;

use App\Entities\Items\SportEquipments;
use App\Http\Controllers\ResourceController;

/** REST API for sport equipment (/api/sport-equipments). */
final class SportEquipmentsController extends ResourceController
{
    public function basePath(): string
    {
        return '/api/sport-equipments';
    }

    protected function table(): string
    {
        return 'sport_equipments';
    }

    protected function writable(): array
    {
        return ['name', 'sport', 'condition', 'purchase_date', 'space_id'];
    }

    protected function format(array $row): array
    {
        return SportEquipments::fromRow($row)->toArray();
    }
}
