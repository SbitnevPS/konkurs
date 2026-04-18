<?php

declare(strict_types=1);

class VkMiniSettingsService
{
    public function get(string $key, $default = null)
    {
        $stmt = Db::pdo()->prepare('SELECT setting_value FROM vk_mini_settings WHERE setting_key = :key LIMIT 1');
        $stmt->execute([':key' => $key]);
        $row = $stmt->fetch();
        return $row ? $row['setting_value'] : $default;
    }

    public function set(string $key, $value): void
    {
        $stmt = Db::pdo()->prepare('INSERT INTO vk_mini_settings (setting_key, setting_value, updated_at) VALUES (:key, :value, :updated_at) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = VALUES(updated_at)');
        $stmt->execute([
            ':key' => $key,
            ':value' => (string) $value,
            ':updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getAll(): array
    {
        $rows = Db::pdo()->query('SELECT setting_key, setting_value FROM vk_mini_settings')->fetchAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['setting_key']] = $row['setting_value'];
        }
        return $result;
    }

    public function saveMany(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set((string) $key, (string) $value);
        }
    }
}
