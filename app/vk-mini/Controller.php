<?php

declare(strict_types=1);

abstract class Controller
{
    protected array $config;

    public function __construct()
    {
        $this->config = require dirname(__DIR__, 2) . '/config.php';
    }

    protected function render(string $view, array $params = []): void
    {
        $authService = new VkMiniAuthService();
        $settingsService = new VkMiniSettingsService();
        $viewData = $params;
        $viewData['currentUser'] = $authService->getCurrentUser();
        $viewData['activePath'] = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
        $viewData['modulePath'] = '/vk-mini';
        $viewData['domain'] = $settingsService->get('vk_mini_site_domain', $_SERVER['HTTP_HOST'] ?? 'localhost');
        $viewData['flash'] = $_SESSION['vk_mini_flash'] ?? null;
        unset($_SESSION['vk_mini_flash']);

        extract($viewData, EXTR_SKIP);
        $contentView = __DIR__ . '/views/' . $view . '.php';
        require __DIR__ . '/views/layout.php';
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['vk_mini_flash'] = ['type' => $type, 'message' => $message];
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['vk_mini_csrf'])) {
            $_SESSION['vk_mini_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['vk_mini_csrf'];
    }

    protected function validateCsrf(?string $token): bool
    {
        return !empty($_SESSION['vk_mini_csrf']) && is_string($token) && hash_equals($_SESSION['vk_mini_csrf'], $token);
    }

    protected function json(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
