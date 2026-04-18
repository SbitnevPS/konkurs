<?php

declare(strict_types=1);

$modulePath = __DIR__;

return [
    'VK_MINI_DB_HOST' => getenv('VK_MINI_DB_HOST') ?: getenv('DB_HOST') ?: '127.0.0.1',
    'VK_MINI_DB_NAME' => getenv('VK_MINI_DB_NAME') ?: getenv('DB_NAME') ?: 'konkurs',
    'VK_MINI_DB_USER' => getenv('VK_MINI_DB_USER') ?: getenv('DB_USER') ?: 'root',
    'VK_MINI_DB_PASS' => getenv('VK_MINI_DB_PASS') ?: getenv('DB_PASS') ?: '',
    'VK_MINI_DB_CHARSET' => getenv('VK_MINI_DB_CHARSET') ?: getenv('DB_CHARSET') ?: 'utf8mb4',
    'VK_MINI_DEFAULT_API_VERSION' => getenv('VK_MINI_DEFAULT_API_VERSION') ?: '5.199',
    'VK_MINI_MODULE_PATH' => getenv('VK_MINI_MODULE_PATH') ?: '/vk-mini',
    'VK_MINI_UPLOAD_DIR' => getenv('VK_MINI_UPLOAD_DIR') ?: $modulePath . '/uploads',
    'VK_MINI_LOG_DIR' => getenv('VK_MINI_LOG_DIR') ?: $modulePath . '/logs',
    'VK_MINI_MAX_FILE_SIZE' => 10 * 1024 * 1024,
];
