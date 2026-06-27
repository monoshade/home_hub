<?php

declare(strict_types=1);

namespace App\Http\Controllers\Spaces;

use App\Entities\Spaces\Apartment;
use App\Entities\Spaces\Deck;
use App\Entities\Spaces\Garage;
use App\Entities\Spaces\House;
use App\Entities\Spaces\Room;
use App\Entities\Spaces\Yard;
use App\Http\Controllers\ResourceController;
use App\Http\Request;

/**
 * REST API for spaces (/api/spaces). Spaces share a single table discriminated
 * by space_type; format() picks the matching entity so each type still defines
 * its own formatted return value. The list endpoint supports ?type= and
 * ?parent= filters.
 */
final class SpaceController extends ResourceController
{
    /** space_type => entity class */
    private const TYPES = [
        'house' => House::class,
        'apartment' => Apartment::class,
        'room' => Room::class,
        'yard' => Yard::class,
        'garage' => Garage::class,
        'deck' => Deck::class,
    ];

    public function basePath(): string
    {
        return '/api/spaces';
    }

    protected function table(): string
    {
        return 'spaces';
    }

    protected function writable(): array
    {
        return [
            'space_type', 'name', 'parent_space_id', 'area', 'description',
            'address', 'lot_size', 'property_type', 'floors', 'year_built',
            'unit_number', 'floor_level', 'type', 'surface_type', 'fenced',
            'capacity', 'attached', 'material', 'covered',
        ];
    }

    protected function format(array $row): array
    {
        $type = $row['space_type'] ?? null;
        $class = self::TYPES[$type] ?? null;

        // Each concrete space type defines its own toArray() (including its
        // intrinsic space_type). parent_space_id is relational metadata, so the
        // controller adds it from the raw row.
        $base = $class
            ? $class::fromRow($row)->toArray()
            : ['id' => isset($row['id']) ? (int) $row['id'] : null, 'name' => $row['name'] ?? null, 'space_type' => $type];

        return $base + [
            'parent_space_id' => isset($row['parent_space_id']) ? (int) $row['parent_space_id'] : null,
        ];
    }

    /** The list endpoint accepts ?type= (space_type) and ?parent= (parent_space_id). */
    protected function allowedQueryParams(): array
    {
        return ['type', 'parent'];
    }

    /** List with optional ?type= (space_type) and ?parent= (parent_space_id) filters. */
    protected function index(Request $req): array
    {
        $this->validateQueryParams($req);

        $filters = [];
        if ($req->query('type') !== null) {
            $filters['space_type'] = $req->query('type');
        }
        if ($req->query('parent') !== null) {
            $filters['parent_space_id'] = (int) $req->query('parent');
        }

        return array_map(fn (array $row) => $this->format($row), $this->repo->all($filters));
    }
}
