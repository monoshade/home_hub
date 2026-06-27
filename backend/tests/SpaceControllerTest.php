<?php

declare(strict_types=1);

use App\Db;
use App\Http\Response;

require_once __DIR__ . '/Support/ApiTestCase.php';

/**
 * SpaceController non-regression suite (pass_to_pass).
 *
 * Spaces are an UNAFFECTED entity: the versioning work targets devices only, so
 * the controller and its behaviour must be identical before and after the change.
 */
final class SpaceControllerTest extends ApiTestCase
{
    /** The six space types SpaceController discriminates (see SpaceController::TYPES). */
    private const SPACE_TYPES = ['house', 'apartment', 'room', 'yard', 'garage', 'deck'];

    /** sha256 of backend/src/Http/Controllers/Spaces/SpaceController.php at the base commit. */
    private const SPACE_CONTROLLER_SHA256 =
        '751944d75ba8d3b39b53c45eeaf14b13baefa6f2e8b1a9bf30ccc784799b7999';

    // --- case 7: controller source must be byte-identical -------------------
    public function testSpaceControllerSourceUnchanged(): void
    {
        $path = __DIR__ . '/../src/Http/Controllers/Spaces/SpaceController.php';
        $this->assertFileExists($path);

        $this->assertSame(
            self::SPACE_CONTROLLER_SHA256,
            hash_file('sha256', $path),
            'SpaceController.php must be unchanged — spaces are an unaffected entity',
        );
    }

    // --- case 8: each space type is queryable WITHOUT a version param -> 200 -
    public function testSpaceTypesQueryableWithoutVersion(): void
    {
        $router = $this->appRouter(Db::Test);

        foreach (self::SPACE_TYPES as $type) {
            $response = $this->call($router, 'GET', '/api/spaces', ['type' => $type]);

            $this->assertInstanceOf(Response::class, $response);
            $this->assertSame(200, $response->status, "GET /api/spaces?type={$type} should return 200");
        }
    }
}
