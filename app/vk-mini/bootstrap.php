<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

spl_autoload_register(static function (string $class): void {
    $base = __DIR__;
    $paths = [
        $base . '/controllers/' . $class . '.php',
        $base . '/services/' . $class . '.php',
        $base . '/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});

require_once __DIR__ . '/Controller.php';
