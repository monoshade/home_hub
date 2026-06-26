<?php

declare(strict_types=1);

namespace App\Http\Controllers\Items;

use App\Entities\Items\Plants;
use App\Http\Controllers\ResourceController;

/** REST API for plants (/api/plants). */
final class PlantsController extends ResourceController
{
    public function basePath(): string
    {
        return '/api/plants';
    }

    protected function table(): string
    {
        return 'plants';
    }

    protected function writable(): array
    {
        return ['name', 'species', 'watering_frequency_days', 'last_watered', 'space_id'];
    }

    protected function format(array $row): array
    {
        return Plants::fromRow($row)->toArray();
    }
}
