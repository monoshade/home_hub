<?php

declare(strict_types=1);

namespace App\Http\Controllers\Items;

use App\Entities\Items\Vehicles;
use App\Http\Controllers\ResourceController;

/** REST API for vehicles (/api/vehicles). */
final class VehiclesController extends ResourceController
{
    public function basePath(): string
    {
        return '/api/vehicles';
    }

    protected function table(): string
    {
        return 'vehicles';
    }

    protected function writable(): array
    {
        return ['name', 'type', 'make', 'model', 'year', 'license_plate', 'space_id'];
    }

    protected function format(array $row): array
    {
        return Vehicles::fromRow($row)->toArray();
    }
}
