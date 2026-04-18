<?php

declare(strict_types=1);

class VkMiniAuthService
{
    public function loginOrCreateUser(array $vkProfile, array $tokenData): array
    {
        $now = date('Y-m-d H:i:s');
        $expiresAt = !empty($tokenData['expires_in']) ? date('Y-m-d H:i:s', time() + (int) $tokenData['expires_in']) : null;
        $vkUserId = (string)($vkProfile['user_id'] ?? $vkProfile['id'] ?? '');

        if ($vkUserId === '') {
            throw new RuntimeException('VK user id is empty');
        }

        $existingStmt = Db::pdo()->prepare('SELECT * FROM vk_mini_users WHERE vk_user_id = :vk_user_id LIMIT 1');
        $existingStmt->execute([':vk_user_id' => $vkUserId]);
        $existing = $existingStmt->fetch();

        if ($existing) {
            $stmt = Db::pdo()->prepare('UPDATE vk_mini_users SET first_name=:first_name,last_name=:last_name,email=:email,avatar_url=:avatar_url,access_token=:access_token,refresh_token=:refresh_token,token_expires_at=:token_expires_at,raw_profile_json=:raw_profile_json,updated_at=:updated_at WHERE id=:id');
            $stmt->execute([
                ':first_name' => $vkProfile['first_name'] ?? null,
                ':last_name' => $vkProfile['last_name'] ?? null,
                ':email' => $vkProfile['email'] ?? null,
                ':avatar_url' => $vkProfile['avatar_url'] ?? null,
                ':access_token' => $tokenData['access_token'] ?? null,
                ':refresh_token' => $tokenData['refresh_token'] ?? null,
                ':token_expires_at' => $expiresAt,
                ':raw_profile_json' => json_encode($vkProfile, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ':updated_at' => $now,
                ':id' => $existing['id'],
            ]);
            $userId = (int)$existing['id'];
        } else {
            $stmt = Db::pdo()->prepare('INSERT INTO vk_mini_users (vk_user_id,first_name,last_name,email,avatar_url,access_token,refresh_token,token_expires_at,raw_profile_json,created_at,updated_at) VALUES (:vk_user_id,:first_name,:last_name,:email,:avatar_url,:access_token,:refresh_token,:token_expires_at,:raw_profile_json,:created_at,:updated_at)');
            $stmt->execute([
                ':vk_user_id' => $vkUserId,
                ':first_name' => $vkProfile['first_name'] ?? null,
                ':last_name' => $vkProfile['last_name'] ?? null,
                ':email' => $vkProfile['email'] ?? null,
                ':avatar_url' => $vkProfile['avatar_url'] ?? null,
                ':access_token' => $tokenData['access_token'] ?? null,
                ':refresh_token' => $tokenData['refresh_token'] ?? null,
                ':token_expires_at' => $expiresAt,
                ':raw_profile_json' => json_encode($vkProfile, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ':created_at' => $now,
                ':updated_at' => $now,
            ]);
            $userId = (int)Db::pdo()->lastInsertId();
        }

        $_SESSION['vk_mini_user_id'] = $userId;
        $_SESSION['vk_mini_login_at'] = $now;

        return $this->getCurrentUser() ?? [];
    }

    public function getCurrentUser(): ?array
    {
        $userId = $_SESSION['vk_mini_user_id'] ?? null;
        if (!$userId) {
            return null;
        }

        $stmt = Db::pdo()->prepare('SELECT * FROM vk_mini_users WHERE id=:id LIMIT 1');
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch();
        if (!$user) {
            return null;
        }

        $user['login_at'] = $_SESSION['vk_mini_login_at'] ?? null;
        return $user;
    }

    public function isAuthorized(): bool
    {
        return $this->getCurrentUser() !== null;
    }

    public function logout(): void
    {
        unset($_SESSION['vk_mini_user_id'], $_SESSION['vk_mini_login_at']);
    }

    public function requireAuth(): void
    {
        if (!$this->isAuthorized()) {
            header('Location: /vk-mini/auth');
            exit;
        }
    }
}
