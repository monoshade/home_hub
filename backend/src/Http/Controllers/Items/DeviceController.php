<?php

declare(strict_types=1);

namespace App\Http\Controllers\Items;

use App\Entities\Items\Device;
use App\Http\Controllers\ResourceController;

/** REST API for devices (/api/devices). */
final class DeviceController extends ResourceController
{
    public function basePath(): string
    {
        return '/api/devices';
    }

    protected function table(): string
    {
        return 'devices';
    }

    protected function writable(): array
    {
        return ['name', 'brand', 'model', 'status', 'purchase_date', 'space_id'];
    }

    protected function format(array $row): array
    {
        return Device::fromRow($row)->toArray();
    }
}
