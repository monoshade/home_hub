<?php

declare(strict_types=1);

use App\Http\Controllers\Items\DeviceController;
use App\Http\Controllers\Items\FurnitureController;
use App\Http\Controllers\Items\InstrumentsController;
use App\Http\Controllers\Items\PlantsController;
use App\Http\Controllers\Items\SportEquipmentsController;
use App\Http\Controllers\Items\VehiclesController;
use App\Http\Controllers\Spaces\SpaceController;
use App\Http\Router;
use App\Context;

/**
 * Build and configure the application router.
 *
 * Each entity owns a dedicated controller (its own REST resource and formatted
 * return value). Two read-only views compose those controllers:
 *
 *   GET /api/health
 *   GET /api/context
 *   {GET,POST,PUT,DELETE} /api/devices[/{id}]            \
 *   {GET,POST,PUT,DELETE} /api/furniture[/{id}]           |
 *   {GET,POST,PUT,DELETE} /api/instruments[/{id}]         | per-item resources
 *   {GET,POST,PUT,DELETE} /api/sport-equipments[/{id}]    |
 *   {GET,POST,PUT,DELETE} /api/plants[/{id}]              |
 *   {GET,POST,PUT,DELETE} /api/vehicles[/{id}]           /
 *   {GET,POST,PUT,DELETE} /api/spaces[/{id}]   (?type= & ?parent= on list)
 *   GET /api/items                  aggregate of all belongings
 *   GET /api/properties             houses/apartments, nested spaces + items
 */
function create_router(PDO $pdo, Context $context): Router
{
    $router = new Router();

    $router->get('/api/health', static fn () => ['status' => 'ok'] + $context->toArray());

    // Runtime context (db + environment) the backend booted with.
    $router->get('/api/context', static fn () => $context->toArray());

    // ---- Per-item resources -----------------------------------------------
    $itemControllers = [
        new DeviceController($pdo),
        new FurnitureController($pdo),
        new InstrumentsController($pdo),
        new SportEquipmentsController($pdo),
        new PlantsController($pdo),
        new VehiclesController($pdo),
    ];
    foreach ($itemControllers as $controller) {
        $controller->register($router);
    }

    // Aggregate of every belonging, each already tagged with its category.
    $router->get('/api/items', static function () use ($itemControllers): array {
        $all = [];
        foreach ($itemControllers as $controller) {
            $all = array_merge($all, $controller->all());
        }
        return $all;
    });

    // ---- Spaces -----------------------------------------------------------
    $spaceController = new SpaceController($pdo);
    $spaceController->register($router);

    // ---- Convenience: properties with nested spaces and located items -----
    $router->get('/api/properties', static function () use ($spaceController, $itemControllers): array {
        $spaces = $spaceController->all();

        $items = [];
        foreach ($itemControllers as $controller) {
            $items = array_merge($items, $controller->all());
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
