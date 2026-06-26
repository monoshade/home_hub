<?php

declare(strict_types=1);

namespace App\Http\Controllers\Items;

use App\Entities\Items\Instruments;
use App\Http\Controllers\ResourceController;

/** REST API for instruments (/api/instruments). */
final class InstrumentsController extends ResourceController
{
    public function basePath(): string
    {
        return '/api/instruments';
    }

    protected function table(): string
    {
        return 'instruments';
    }

    protected function writable(): array
    {
        return ['name', 'type', 'brand', 'purchase_date', 'space_id'];
    }

    protected function format(array $row): array
    {
        return Instruments::fromRow($row)->toArray();
    }
}
