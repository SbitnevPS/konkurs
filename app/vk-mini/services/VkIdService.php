<?php

declare(strict_types=1);

class VkIdService
{
    public function __construct(
        private readonly VkMiniSettingsService $settings = new VkMiniSettingsService(),
        private readonly VkMiniLogService $logger = new VkMiniLogService()
    ) {}

    public function getAuthorizeUrl(): string
    {
        $state = bin2hex(random_bytes(16));
        $_SESSION['vk_mini_oauth_state'] = $state;

        $params = [
            'client_id' => $this->settings->get('vkid_client_id', ''),
            'response_type' => 'code',
            'redirect_uri' => $this->settings->get('vkid_redirect_uri', ''),
            'scope' => $this->settings->get('vkid_scope', 'email'),
            'state' => $state,
            'v' => $this->settings->get('vkid_api_version', '5.199'),
        ];

        $authorizeUrl = rtrim($this->settings->get('vkid_authorize_url', 'https://id.vk.com/authorize'), '?');
        $url = $authorizeUrl . '?' . http_build_query($params);
        $this->logger->info('auth_start', 'VK ID authorize URL generated');

        return $url;
    }

    public function exchangeCodeForToken(string $code): array
    {
        $tokenUrl = $this->settings->get('vkid_token_url', 'https://id.vk.com/oauth2/auth');
        $payload = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->settings->get('vkid_client_id', ''),
            'client_secret' => $this->settings->get('vkid_client_secret', ''),
            'redirect_uri' => $this->settings->get('vkid_redirect_uri', ''),
        ];

        $this->logger->info('exchange_code_token', 'Exchange code for token started', ['request' => ['url' => $tokenUrl, 'method' => 'POST', 'payload' => $payload]]);
        return $this->httpPost($tokenUrl, $payload);
    }

    public function fetchUserInfo(string $accessToken): array
    {
        $url = $this->settings->get('vkid_userinfo_url', 'https://id.vk.com/oauth2/user_info');
        $payload = ['access_token' => $accessToken];
        $this->logger->info('fetch_user_info', 'VK user info request', ['request' => ['url' => $url, 'method' => 'POST', 'payload' => $payload]]);
        return $this->httpPost($url, $payload);
    }

    private function httpPost(string $url, array $payload): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_POSTFIELDS => http_build_query($payload),
            CURLOPT_TIMEOUT => 20,
        ]);
        $raw = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($raw === false || $error !== '') {
            $this->logger->error('http_error', 'HTTP error while VK request', ['response' => ['code' => $code, 'payload' => ['error' => $error]]]);
            throw new RuntimeException('VK request failed: ' . $error);
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            $this->logger->error('json_error', 'JSON decode failed', ['response' => ['code' => $code, 'payload' => ['raw' => $raw]]]);
            throw new RuntimeException('Invalid VK JSON response');
        }

        return $decoded;
    }
}
