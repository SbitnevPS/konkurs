<?php

declare(strict_types=1);

$vkMiniConfig = require __DIR__ . '/app/vk-mini/config.php';

return [
    'app_name' => 'konkurs',
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'name' => getenv('DB_NAME') ?: 'pavel-sbitnev_test',
        'user' => getenv('DB_USER') ?: '046613795_test',
        'pass' => getenv('DB_PASS') ?: 'RwfgwR5hnT7@',
        'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
    ],
    'vk_mini' => $vkMiniConfig,
];
