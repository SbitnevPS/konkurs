<?php

declare(strict_types=1);

require_once __DIR__ . '/app/vk-mini/bootstrap.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

$routes = [
    'GET' => [
        '/vk-mini' => [AuthController::class, 'index'],
        '/vk-mini/auth' => [AuthController::class, 'index'],
        '/vk-mini/auth/login' => [AuthController::class, 'login'],
        '/vk-mini/auth/callback' => [AuthController::class, 'callback'],
        '/vk-mini/logout' => [AuthController::class, 'logout'],
        '/vk-mini/publish' => [PublishController::class, 'index'],
        '/vk-mini/settings' => [SettingsController::class, 'index'],
        '/vk-mini/diagnostics' => [DiagnosticsController::class, 'index'],
        '/vk-mini/api/diagnostics/test-group-token' => [DiagnosticsController::class, 'testGroupToken'],
    ],
    'POST' => [
        '/vk-mini/publish/send' => [PublishController::class, 'send'],
        '/vk-mini/settings/save' => [SettingsController::class, 'save'],
        '/vk-mini/api/upload-image' => [PublishController::class, 'uploadImage'],
    ],
];

if (isset($routes[$method][$path])) {
    [$controllerClass, $action] = $routes[$method][$path];
    $controller = new $controllerClass();
    $controller->{$action}();
    return;
}

http_response_code(404);
echo 'Not Found';
