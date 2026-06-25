<?php

declare(strict_types=1);

// Minimal PSR-4 autoloader for the App\ namespace. Used as a fallback when
// Composer's vendor/autoload.php is unavailable (e.g. under the docker-compose
// bind mount, which hides the image's vendor/ directory).
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});
