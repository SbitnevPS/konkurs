<?php

declare(strict_types=1);

class VkMiniUploadService
{
    private array $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    public function __construct(
        private readonly VkMiniLogService $logger = new VkMiniLogService()
    ) {}

    public function validateUploadedImage(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Image upload error'];
        }

        $ext = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowed, true)) {
            return ['ok' => false, 'error' => 'Invalid file extension'];
        }

        $maxBytes = (int)((require __DIR__ . '/../config.php')['VK_MINI_MAX_FILE_SIZE'] ?? 10 * 1024 * 1024);
        if ((int)$file['size'] > $maxBytes) {
            return ['ok' => false, 'error' => 'File is too large'];
        }

        $mime = mime_content_type((string)$file['tmp_name']);
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return ['ok' => false, 'error' => 'Invalid mime type'];
        }

        $this->logger->info('validate_image', 'Uploaded image validated', ['name' => $file['name'], 'size' => $file['size']]);
        return ['ok' => true];
    }

    public function storeUploadedImage(array $file): array
    {
        $config = require __DIR__ . '/../config.php';
        $dir = $config['VK_MINI_UPLOAD_DIR'];
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $safe = $this->buildSafeFilename((string)$file['name']);
        $target = rtrim($dir, '/') . '/' . $safe;
        if (!move_uploaded_file((string)$file['tmp_name'], $target)) {
            throw new RuntimeException('Cannot store uploaded image');
        }

        $this->logger->info('store_image', 'Image stored', ['local_image_path' => $target]);

        return ['path' => $target, 'filename' => $safe, 'public' => '/app/vk-mini/uploads/' . rawurlencode($safe)];
    }

    public function buildSafeFilename(string $originalName): string
    {
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $base = preg_replace('/[^a-zA-Z0-9_-]+/', '-', pathinfo($originalName, PATHINFO_FILENAME)) ?: 'image';
        return trim($base, '-') . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    }
}
