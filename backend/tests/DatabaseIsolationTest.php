<?php

declare(strict_types=1);

use App\Db;

require_once __DIR__ . '/Support/ApiTestCase.php';

/**
 * DB isolation / non-pollution suite (pass_to_pass).
 *
 * Each test snapshots a database, exercises the solution with writes in the
 * `test` context, then re-snapshots and asserts nothing changed elsewhere:
 *   - prod/demo must be wholly untouched (no cross-DB pollution);
 *   - the unaffected `spaces` entity must be untouched in EVERY context DB
 *     (the exercise only writes `devices`).
 *
 * The exercise uses plain device CRUD so it is valid on both the base and the
 * solved repo, which is what keeps these tests pass_to_pass.
 */
final class DatabaseIsolationTest extends ApiTestCase
{
    /** Create + update + delete a device through the app in the `test` context. */
    private function exerciseTestContextWrites(): void
    {
        $router = $this->appRouter(Db::Test);

        $created = $this->call($router, 'POST', '/api/devices', [], [
            'name' => 'Isolation Probe',
            'brand' => 'ACME',
            'status' => 'working',
        ]);
        $this->assertSame(201, $created->status, 'device create should succeed in the test context');

        $id = is_array($created->data) ? ($created->data['id'] ?? null) : null;
        $this->assertNotNull($id, 'create response must carry the new id');

        $this->call($router, 'PUT', "/api/devices/{$id}", [], ['status' => 'broken']);
        $this->call($router, 'DELETE', "/api/devices/{$id}");
    }

    // --- case 1 ------------------------------------------------------------
    public function testProdSchemaUntouched(): void
    {
        $before = $this->schemaSnapshot($this->pdo('prod'));
        $this->exerciseTestContextWrites();
        $after = $this->schemaSnapshot($this->pdo('prod'));

        $this->assertEquals($before, $after, 'prod schema must be unchanged by test-context writes');
    }

    // --- case 2 ------------------------------------------------------------
    public function testProdDataUntouched(): void
    {
        $before = $this->dataSnapshot($this->pdo('prod'));
        $this->exerciseTestContextWrites();
        $after = $this->dataSnapshot($this->pdo('prod'));

        $this->assertEquals($before, $after, 'prod data must be unchanged by test-context writes');
    }

    // --- case 3 ------------------------------------------------------------
    public function testDemoSchemaUntouched(): void
    {
        $before = $this->schemaSnapshot($this->pdo('demo'));
        $this->exerciseTestContextWrites();
        $after = $this->schemaSnapshot($this->pdo('demo'));

        $this->assertEquals($before, $after, 'demo schema must be unchanged by test-context writes');
    }

    // --- case 4 ------------------------------------------------------------
    public function testDemoDataUntouched(): void
    {
        $before = $this->dataSnapshot($this->pdo('demo'));
        $this->exerciseTestContextWrites();
        $after = $this->dataSnapshot($this->pdo('demo'));

        $this->assertEquals($before, $after, 'demo data must be unchanged by test-context writes');
    }

    // --- case 5 ------------------------------------------------------------
    public function testSpacesSchemaUntouchedAcrossContexts(): void
    {
        foreach (['prod', 'demo', 'test'] as $db) {
            $before = $this->schemaSnapshot($this->pdo($db), 'spaces');
            $this->exerciseTestContextWrites();
            $after = $this->schemaSnapshot($this->pdo($db), 'spaces');

            $this->assertEquals($before, $after, "spaces schema must be unchanged in {$db}");
        }
    }

    // --- case 6 ------------------------------------------------------------
    public function testSpacesDataUntouchedAcrossContexts(): void
    {
        foreach (['prod', 'demo', 'test'] as $db) {
            $before = $this->dataSnapshot($this->pdo($db), ['spaces']);
            $this->exerciseTestContextWrites();
            $after = $this->dataSnapshot($this->pdo($db), ['spaces']);

            $this->assertEquals($before, $after, "spaces data must be unchanged in {$db}");
        }
    }
}
