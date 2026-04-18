<?php

declare(strict_types=1);

class DiagnosticsController extends Controller
{
    private VkMiniAuthService $auth;
    private VkMiniSettingsService $settings;
    private VkMiniLogService $logger;
    private VkApiService $vkApi;

    public function __construct()
    {
        parent::__construct();
        $this->auth = new VkMiniAuthService();
        $this->settings = new VkMiniSettingsService();
        $this->logger = new VkMiniLogService();
        $this->vkApi = new VkApiService();
    }

    public function index(): void
    {
        $user = $this->auth->getCurrentUser();
        $lastPost = Db::pdo()->query('SELECT * FROM vk_mini_posts ORDER BY id DESC LIMIT 1')->fetch() ?: null;
        $limit = (int)$this->settings->get('vk_mini_log_entries_limit', 50);
        $logs = $this->logger->getRecent($limit);

        $this->render('diagnostics', [
            'user' => $user,
            'settings' => $this->settings->getAll(),
            'lastPost' => $lastPost,
            'logs' => $logs,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function testGroupToken(): void
    {
        try {
            $result = $this->vkApi->testGroupToken();
            $this->logger->info('test_group_token', 'Group token test finished', ['response' => ['payload' => $result]]);
            $this->json(['ok' => true, 'result' => $result]);
        } catch (Throwable $e) {
            $this->logger->error('test_group_token_failed', $e->getMessage());
            $this->json(['ok' => false, 'error' => $e->getMessage()], 422);
        }
    }
}
