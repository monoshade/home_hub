<?php

declare(strict_types=1);

// Prefer Composer's autoloader; fall back to the bundled PSR-4 loader (the
// docker-compose bind mount hides the image's vendor/ directory).
$vendor = __DIR__ . '/../vendor/autoload.php';
require is_file($vendor) ? $vendor : __DIR__ . '/../src/autoload.php';
require __DIR__ . '/../src/routes.php';

use App\Context;
use App\Database;
use App\Environment;
use App\Http\HttpException;
use App\Http\Request;
use App\Http\Response;

// --- Initiation: resolve the runtime context (db + environment) at startup ---
$context = Context::fromEnv();

// --- CORS (open outside prod for local dev; tighten in prod) ---
$allowedOrigin = $context->environment === Environment::Prod
    ? (getenv('CORS_ALLOW_ORIGIN') ?: '')
    : '*';
if ($allowedOrigin !== '') {
    header('Access-Control-Allow-Origin: ' . $allowedOrigin);
}
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    $router = create_router(Database::connection($context->db), $context);
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
