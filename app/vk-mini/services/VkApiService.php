<?php

declare(strict_types=1);

class VkApiService
{
    public function __construct(
        private readonly VkMiniSettingsService $settings = new VkMiniSettingsService(),
        private readonly VkMiniLogService $logger = new VkMiniLogService()
    ) {}

    public function call(string $method, array $params, ?string $token = null): array
    {
        $version = $params['v'] ?? $this->settings->get('vk_api_version', $this->settings->get('vkid_api_version', '5.199'));
        $url = 'https://api.vk.com/method/' . $method;
        $payload = $params + ['v' => $version];
        if ($token) {
            $payload['access_token'] = $token;
        }

        $this->logger->debug('vk_api_call', 'VK API call', ['request' => ['url' => $url, 'method' => 'POST', 'payload' => $payload], 'method' => $method]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => http_build_query($payload),
            CURLOPT_TIMEOUT => 25,
        ]);
        $raw = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($raw === false || $err !== '') {
            $this->logger->error('vk_http_error', 'VK API transport error', ['response' => ['code' => $code, 'payload' => ['error' => $err]], 'method' => $method]);
            throw new RuntimeException('VK HTTP error: ' . $err);
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            $this->logger->error('vk_json_error', 'VK API invalid json', ['response' => ['code' => $code, 'payload' => ['raw' => $raw]], 'method' => $method]);
            throw new RuntimeException('VK JSON decode error');
        }

        $this->logger->debug('vk_api_response', 'VK API response', ['response' => ['code' => $code, 'payload' => $decoded], 'method' => $method]);
        return $decoded;
    }

    public function testGroupToken(): array
    {
        $groupId = (string)$this->settings->get('vk_group_id', '');
        $token = (string)$this->settings->get('vk_group_token', '');
        if ($groupId === '' || $token === '') {
            throw new RuntimeException('Group settings are not configured');
        }

        return $this->call('wall.get', ['owner_id' => '-' . ltrim($groupId, '-') , 'count' => 1], $token);
    }

    public function getWallUploadServer(string $groupId, string $token): array
    {
        return $this->call('photos.getWallUploadServer', ['group_id' => $groupId], $token);
    }

    public function uploadPhotoToUrl(string $uploadUrl, string $filePath): array
    {
        $ch = curl_init($uploadUrl);
        $payload = ['photo' => new CURLFile($filePath)];
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 30,
        ]);
        $raw = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($raw === false || $err !== '') {
            throw new RuntimeException('VK upload error: ' . $err);
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Invalid VK upload JSON');
        }

        $this->logger->info('upload_file', 'VK upload finished', ['response' => ['code' => $code, 'payload' => $decoded]]);
        return $decoded;
    }

    public function saveWallPhoto(array $uploadResponse, string $groupId, string $token): array
    {
        return $this->call('photos.saveWallPhoto', [
            'group_id' => $groupId,
            'photo' => $uploadResponse['photo'] ?? '',
            'server' => $uploadResponse['server'] ?? '',
            'hash' => $uploadResponse['hash'] ?? '',
        ], $token);
    }

    public function postToWall(string $groupId, string $token, string $message, array $attachments = [], bool $fromGroup = true): array
    {
        return $this->call('wall.post', [
            'owner_id' => '-' . ltrim($groupId, '-'),
            'from_group' => $fromGroup ? 1 : 0,
            'message' => $message,
            'attachments' => implode(',', $attachments),
        ], $token);
    }
}
