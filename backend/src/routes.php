<?php

declare(strict_types=1);

use App\Entities\Items\Device;
use App\Entities\Items\Furniture;
use App\Entities\Items\Instruments;
use App\Entities\Items\Plants;
use App\Entities\Items\SportEquipments;
use App\Entities\Items\Vehicles;
use App\Entities\Spaces\Apartment;
use App\Entities\Spaces\Deck;
use App\Entities\Spaces\Garage;
use App\Entities\Spaces\House;
use App\Entities\Spaces\Room;
use App\Entities\Spaces\Yard;
use App\Http\HttpException;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;
use App\Repository\Repository;

/**
 * Build and configure the application router.
 *
 * Routes:
 *   GET    /api/health
 *   GET    /api/items                        aggregate of all belongings
 *   GET    /api/properties                   houses/apartments, nested spaces + items
 *   {GET,POST,PUT,DELETE} /api/spaces[/{id}]
 *   {GET,POST,PUT,DELETE} /api/{category}[/{id}]   per item category
 */
function create_router(PDO $pdo): Router
{
    $router = new Router();

    $router->get('/api/health', static fn () => ['status' => 'ok']);

    // ---- Item resources (one table each) ----------------------------------
    // path => [entity class, writable columns]
    $itemResources = [
        'devices'          => [Device::class,          ['name', 'brand', 'model', 'status', 'purchase_date', 'space_id']],
        'furniture'        => [Furniture::class,       ['name', 'material', 'dimensions', 'purchase_date', 'space_id']],
        'instruments'      => [Instruments::class,     ['name', 'type', 'brand', 'purchase_date', 'space_id']],
        'sport-equipments' => [SportEquipments::class, ['name', 'sport', 'condition', 'purchase_date', 'space_id']],
        'plants'           => [Plants::class,          ['name', 'species', 'watering_frequency_days', 'last_watered', 'space_id']],
        'vehicles'         => [Vehicles::class,        ['name', 'type', 'make', 'model', 'year', 'license_plate', 'space_id']],
    ];

    /** @var array<string, Repository> $itemRepos */
    $itemRepos = [];
    foreach ($itemResources as $path => [$class, $columns]) {
        $table = str_replace('-', '_', $path);
        $serialize = static fn (array $row): array => $class::fromRow($row)->toArray() + ['category' => $path];
        $repo = new Repository($pdo, $table, $columns, $serialize);
        $itemRepos[$path] = $repo;
        register_crud($router, "/api/$path", $repo);
    }

    // Aggregate of every belonging, each tagged with its category.
    $router->get('/api/items', static function () use ($itemRepos): array {
        $all = [];
        foreach ($itemRepos as $repo) {
            $all = array_merge($all, $repo->all());
        }
        return $all;
    });

    // ---- Spaces (single table, discriminated by space_type) ---------------
    $spaceTypes = [
        'house' => House::class,
        'apartment' => Apartment::class,
        'room' => Room::class,
        'yard' => Yard::class,
        'garage' => Garage::class,
        'deck' => Deck::class,
    ];
    $spaceColumns = [
        'space_type', 'name', 'parent_space_id', 'area', 'description',
        'address', 'lot_size', 'property_type', 'floors', 'year_built',
        'unit_number', 'floor_level', 'type', 'surface_type', 'fenced',
        'capacity', 'attached', 'material', 'covered',
    ];
    // space_type and parent_space_id aren't entity properties, so inject them.
    $serializeSpace = static function (array $row) use ($spaceTypes): array {
        $type = $row['space_type'] ?? null;
        $class = $spaceTypes[$type] ?? null;
        $base = $class
            ? $class::fromRow($row)->toArray()
            : ['id' => isset($row['id']) ? (int) $row['id'] : null, 'name' => $row['name'] ?? null];

        // Drop the entity's in-memory relation collections (always empty when
        // loaded from a row); containment is expressed via parent_space_id and
        // the nested `spaces`/`items` arrays the /api/properties endpoint adds.
        unset($base['rooms'], $base['yards'], $base['garages'], $base['decks'], $base['garage']);

        return $base + [
            'space_type' => $type,
            'parent_space_id' => isset($row['parent_space_id']) ? (int) $row['parent_space_id'] : null,
        ];
    };
    $spaceRepo = new Repository($pdo, 'spaces', $spaceColumns, $serializeSpace);

    // Custom list with ?type= / ?parent= filters; CRUD for the rest.
    $router->get('/api/spaces', static function (Request $req) use ($spaceRepo): array {
        $filters = [];
        if ($req->query('type') !== null) {
            $filters['space_type'] = $req->query('type');
        }
        if ($req->query('parent') !== null) {
            $filters['parent_space_id'] = (int) $req->query('parent');
        }
        return $spaceRepo->all($filters);
    });
    register_crud($router, '/api/spaces', $spaceRepo, registerList: false);

    // ---- Convenience: properties with nested spaces and located items -----
    $router->get('/api/properties', static function () use ($spaceRepo, $itemRepos): array {
        $spaces = $spaceRepo->all();

        $items = [];
        foreach ($itemRepos as $repo) {
            $items = array_merge($items, $repo->all());
        }

        $itemsBySpace = [];
        foreach ($items as $item) {
            if ($item['space_id'] !== null) {
                $itemsBySpace[$item['space_id']][] = $item;
            }
        }

        // Two-level tree: properties (top-level) -> contained spaces.
        $properties = [];
        $childrenByParent = [];
        foreach ($spaces as $space) {
            $space['items'] = $itemsBySpace[$space['id']] ?? [];
            $isProperty = in_array($space['space_type'], ['house', 'apartment'], true);
            if ($isProperty && $space['parent_space_id'] === null) {
                $properties[$space['id']] = $space;
            } else {
                $childrenByParent[$space['parent_space_id']][] = $space;
            }
        }
        foreach ($properties as $id => &$property) {
            $property['spaces'] = $childrenByParent[$id] ?? [];
        }
        unset($property);

        return array_values($properties);
    });

    return $router;
}

/**
 * Register GET (list), GET/{id}, POST, PUT/{id}, DELETE/{id} for a resource.
 */
function register_crud(Router $router, string $base, Repository $repo, bool $registerList = true): void
{
    if ($registerList) {
        $router->get($base, static fn () => $repo->all());
    }

    $router->get("$base/{id}", static function (Request $req) use ($repo): array {
        $row = $repo->find((int) $req->vars['id']);
        if ($row === null) {
            throw HttpException::notFound();
        }
        return $row;
    });

    $router->post($base, static fn (Request $req) => Response::json($repo->create($req->body()), 201));

    $router->put("$base/{id}", static function (Request $req) use ($repo): array {
        $row = $repo->update((int) $req->vars['id'], $req->body());
        if ($row === null) {
            throw HttpException::notFound();
        }
        return $row;
    });

    $router->delete("$base/{id}", static function (Request $req) use ($repo): Response {
        if (!$repo->delete((int) $req->vars['id'])) {
            throw HttpException::notFound();
        }
        return new Response(null, 204);
    });
}
