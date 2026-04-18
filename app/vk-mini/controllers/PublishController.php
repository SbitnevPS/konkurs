<?php

declare(strict_types=1);

class PublishController extends Controller
{
    private VkMiniAuthService $auth;
    private VkMiniSettingsService $settings;
    private VkApiService $vkApi;
    private VkMiniUploadService $upload;
    private VkMiniLogService $logger;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new VkMiniAuthService();
        $this->settings = new VkMiniSettingsService();
        $this->vkApi = new VkApiService();
        $this->upload = new VkMiniUploadService();
        $this->logger = new VkMiniLogService();
    }

    public function index(): void
    {
        $this->auth->requireAuth();
        $this->logger->info('open_publish', 'Publish page opened');
        $this->render('publish', ['csrfToken' => $this->csrfToken()]);
    }

    public function send(): void
    {
        $this->auth->requireAuth();
        if (!$this->validateCsrf($_POST['_csrf'] ?? null)) {
            $this->setFlash('error', 'Ошибка CSRF токена.');
            $this->redirect('/vk-mini/publish');
        }

        $groupId = trim((string)$this->settings->get('vk_group_id', ''));
        $groupToken = trim((string)$this->settings->get('vk_group_token', ''));
        $message = trim((string)($_POST['post_text'] ?? ''));

        if ($groupId === '' || $groupToken === '') {
            $this->setFlash('error', 'Заполните vk_group_id и vk_group_token в настройках.');
            $this->redirect('/vk-mini/publish');
        }

        $user = $this->auth->getCurrentUser();
        $stmt = Db::pdo()->prepare('INSERT INTO vk_mini_posts (user_id, group_id, post_text, status, created_at) VALUES (:user_id,:group_id,:post_text,:status,:created_at)');
        $stmt->execute([
            ':user_id' => $user['id'] ?? null,
            ':group_id' => $groupId,
            ':post_text' => $message,
            ':status' => 'pending',
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
        $postId = (int)Db::pdo()->lastInsertId();

        $attachments = [];
        $localImagePath = null;
        $rawResponse = [];

        try {
            if (!empty($_FILES['image']['name'])) {
                $valid = $this->upload->validateUploadedImage($_FILES['image']);
                if (!$valid['ok']) {
                    throw new RuntimeException($valid['error']);
                }

                $stored = $this->upload->storeUploadedImage($_FILES['image']);
                $localImagePath = $stored['path'];

                $uploadServer = $this->vkApi->getWallUploadServer($groupId, $groupToken);
                $this->logger->info('get_upload_server', 'photos.getWallUploadServer done', ['response' => ['payload' => $uploadServer]]);
                $uploadUrl = (string)($uploadServer['response']['upload_url'] ?? '');
                if ($uploadUrl === '') {
                    throw new RuntimeException('upload_url is empty');
                }

                $uploadResponse = $this->vkApi->uploadPhotoToUrl($uploadUrl, $localImagePath);
                $saveResponse = $this->vkApi->saveWallPhoto($uploadResponse, $groupId, $groupToken);
                $photo = $saveResponse['response'][0] ?? [];
                $attachment = 'photo' . ($photo['owner_id'] ?? '') . '_' . ($photo['id'] ?? '');
                if ($attachment !== 'photo_') {
                    $attachments[] = $attachment;
                }
                $rawResponse['upload'] = $uploadResponse;
                $rawResponse['save'] = $saveResponse;
            }

            $fromGroup = ((string)$this->settings->get('vk_publish_from_group', '1') === '1');
            $postResponse = $this->vkApi->postToWall($groupId, $groupToken, $message, $attachments, $fromGroup);
            $rawResponse['post'] = $postResponse;

            $stmt = Db::pdo()->prepare('UPDATE vk_mini_posts SET local_image_path=:local_image_path,vk_photo_id=:vk_photo_id,vk_owner_id=:vk_owner_id,vk_post_id=:vk_post_id,vk_response_json=:vk_response_json,status=:status,error_message=:error_message WHERE id=:id');
            $stmt->execute([
                ':local_image_path' => $localImagePath,
                ':vk_photo_id' => $attachments[0] ?? null,
                ':vk_owner_id' => isset($postResponse['response']['post_id']) ? ('-' . ltrim($groupId, '-')) : null,
                ':vk_post_id' => $postResponse['response']['post_id'] ?? null,
                ':vk_response_json' => json_encode($rawResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ':status' => isset($postResponse['response']['post_id']) ? 'success' : 'error',
                ':error_message' => isset($postResponse['error']) ? json_encode($postResponse['error'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                ':id' => $postId,
            ]);

            $this->setFlash('success', 'Публикация отправлена. post_id: ' . (($postResponse['response']['post_id'] ?? 'не получен')));
        } catch (Throwable $e) {
            $this->logger->error('publish_failed', $e->getMessage());
            $stmt = Db::pdo()->prepare('UPDATE vk_mini_posts SET local_image_path=:local_image_path,status=:status,error_message=:error_message,vk_response_json=:vk_response_json WHERE id=:id');
            $stmt->execute([
                ':local_image_path' => $localImagePath,
                ':status' => 'error',
                ':error_message' => $e->getMessage(),
                ':vk_response_json' => json_encode($rawResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ':id' => $postId,
            ]);
            $this->setFlash('error', 'Ошибка публикации: ' . $e->getMessage());
        }

        $this->redirect('/vk-mini/publish');
    }

    public function uploadImage(): void
    {
        $this->auth->requireAuth();

        if (empty($_FILES['image'])) {
            $this->json(['ok' => false, 'error' => 'No file'], 422);
        }

        $valid = $this->upload->validateUploadedImage($_FILES['image']);
        if (!$valid['ok']) {
            $this->json($valid, 422);
        }

        $stored = $this->upload->storeUploadedImage($_FILES['image']);
        $this->json(['ok' => true, 'file' => $stored]);
    }
}
