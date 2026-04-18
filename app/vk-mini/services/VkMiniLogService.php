<?php

declare(strict_types=1);

class VkMiniLogService
{
    public function info(string $action, string $message, array $context = []): void { $this->log('info', $action, $message, $context); }
    public function warning(string $action, string $message, array $context = []): void { $this->log('warning', $action, $message, $context); }
    public function error(string $action, string $message, array $context = []): void { $this->log('error', $action, $message, $context); }
    public function debug(string $action, string $message, array $context = []): void { $this->log('debug', $action, $message, $context); }

    public function log(string $level, string $action, string $message, array $context = []): void
    {
        $masked = $this->maskSensitive($context);
        $request = $masked['request'] ?? [];
        $response = $masked['response'] ?? [];

        $stmt = Db::pdo()->prepare('INSERT INTO vk_mini_logs (level, channel, action, message, request_url, request_method, request_payload, response_code, response_payload, context_json, created_at) VALUES (:level, :channel, :action, :message, :request_url, :request_method, :request_payload, :response_code, :response_payload, :context_json, :created_at)');
        $stmt->execute([
            ':level' => $level,
            ':channel' => $masked['channel'] ?? 'vk-mini',
            ':action' => $action,
            ':message' => $message,
            ':request_url' => $request['url'] ?? null,
            ':request_method' => $request['method'] ?? null,
            ':request_payload' => isset($request['payload']) ? json_encode($request['payload'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            ':response_code' => $response['code'] ?? null,
            ':response_payload' => isset($response['payload']) ? json_encode($response['payload'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            ':context_json' => json_encode($masked, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getRecent(int $limit = 50): array
    {
        $stmt = Db::pdo()->prepare('SELECT * FROM vk_mini_logs ORDER BY id DESC LIMIT :limit');
        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function maskToken(?string $token): ?string
    {
        if (!$token) {
            return $token;
        }
        $len = strlen($token);
        if ($len <= 8) {
            return str_repeat('*', $len);
        }
        return substr($token, 0, 4) . str_repeat('*', max(1, $len - 8)) . substr($token, -4);
    }

    private function maskSensitive(array $context): array
    {
        array_walk_recursive($context, function (&$value, $key): void {
            if (!is_string($value)) {
                return;
            }
            if (preg_match('/token|secret|password/i', (string) $key)) {
                $value = $this->maskToken($value);
            }
        });

        return $context;
    }
}
