<?php

declare(strict_types=1);

class SettingsController extends Controller
{
    private VkMiniSettingsService $settings;

    private array $fields = [
        'vkid_client_id', 'vkid_client_secret', 'vkid_redirect_uri', 'vkid_authorize_url', 'vkid_token_url',
        'vkid_userinfo_url', 'vkid_scope', 'vkid_api_version', 'vk_group_id', 'vk_group_token', 'vk_publish_from_group',
        'vk_api_version', 'vk_mini_site_domain', 'vk_mini_module_path', 'vk_mini_debug_enabled', 'vk_mini_log_entries_limit',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->settings = new VkMiniSettingsService();
    }

    public function index(): void
    {
        $settings = $this->settings->getAll();
        $this->render('settings', [
            'settings' => $settings,
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function save(): void
    {
        if (!$this->validateCsrf($_POST['_csrf'] ?? null)) {
            $this->setFlash('error', 'Ошибка CSRF токена.');
            $this->redirect('/vk-mini/settings');
        }

        $current = $this->settings->getAll();
        $secretFields = ['vkid_client_secret', 'vk_group_token'];

        foreach ($this->fields as $field) {
            if (in_array($field, ['vk_publish_from_group', 'vk_mini_debug_enabled'], true)) {
                $this->settings->set($field, isset($_POST[$field]) ? '1' : '0');
                continue;
            }

            $value = trim((string)($_POST[$field] ?? ''));
            if (in_array($field, $secretFields, true) && $value === '') {
                continue;
            }
            if ($value === '' && isset($current[$field])) {
                continue;
            }
            $this->settings->set($field, $value);
        }

        $this->setFlash('success', 'Настройки сохранены.');
        $this->redirect('/vk-mini/settings');
    }
}
