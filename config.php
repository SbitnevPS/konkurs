<?php

declare(strict_types=1);

$vkMiniConfig = require __DIR__ . '/app/vk-mini/config.php';

return [
    'app_name' => 'konkurs',
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'name' => getenv('DB_NAME') ?: 'konkurs',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
    'vk_mini' => $vkMiniConfig,
];
