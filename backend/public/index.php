<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Database;

// --- CORS (open for local dev; tighten before production) ---
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// --- Minimal router. Replace with a real router as the app grows. ---
try {
    switch (true) {
        case $method === 'GET' && $path === '/api/health':
            echo json_encode(['status' => 'ok']);
            break;

        case $method === 'GET' && $path === '/api/items':
            $stmt = Database::connection()->query('SELECT id, name, created_at FROM items ORDER BY id');
            echo json_encode($stmt->fetchAll());
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error', 'detail' => $e->getMessage()]);
}
