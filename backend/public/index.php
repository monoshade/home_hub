<?php

declare(strict_types=1);

// Prefer Composer's autoloader; fall back to the bundled PSR-4 loader (the
// docker-compose bind mount hides the image's vendor/ directory).
$vendor = __DIR__ . '/../vendor/autoload.php';
require is_file($vendor) ? $vendor : __DIR__ . '/../src/autoload.php';
require __DIR__ . '/../src/routes.php';

use App\Database;
use App\Http\HttpException;
use App\Http\Request;
use App\Http\Response;

// --- CORS (open for local dev; tighten before production) ---
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $router = create_router(Database::connection());
    $response = $router->dispatch(Request::fromGlobals());
} catch (HttpException $e) {
    $response = Response::json(['error' => $e->getMessage()], $e->status);
} catch (InvalidArgumentException $e) {
    $response = Response::json(['error' => $e->getMessage()], 400);
} catch (Throwable $e) {
    // FK / unique / check violations surface as PDOExceptions -> 400.
    $status = $e instanceof PDOException ? 400 : 500;
    $response = Response::json(['error' => 'Request failed', 'detail' => $e->getMessage()], $status);
}

$response->send();
