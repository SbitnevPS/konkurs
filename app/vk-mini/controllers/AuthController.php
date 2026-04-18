<?php

declare(strict_types=1);

class AuthController extends Controller
{
    private VkIdService $vkId;
    private VkMiniAuthService $auth;
    private VkMiniLogService $logger;

    public function __construct()
    {
        parent::__construct();
        $this->vkId = new VkIdService();
        $this->auth = new VkMiniAuthService();
        $this->logger = new VkMiniLogService();
    }

    public function index(): void
    {
        $this->render('auth', [
            'authorizeUrl' => $this->vkId->getAuthorizeUrl(),
            'csrfToken' => $this->csrfToken(),
        ]);
    }

    public function login(): void
    {
        $this->logger->info('auth_start', 'User starts login flow');
        $this->redirect($this->vkId->getAuthorizeUrl());
    }

    public function callback(): void
    {
        $this->logger->info('auth_callback', 'OAuth callback entered', ['query' => $_GET]);

        $state = (string)($_GET['state'] ?? '');
        if ($state === '' || !hash_equals((string)($_SESSION['vk_mini_oauth_state'] ?? ''), $state)) {
            $this->setFlash('error', 'Некорректный state в callback.');
            $this->redirect('/vk-mini/auth');
        }

        $code = (string)($_GET['code'] ?? '');
        if ($code === '') {
            $this->setFlash('error', 'Пустой code в callback.');
            $this->redirect('/vk-mini/auth');
        }

        try {
            $tokenData = $this->vkId->exchangeCodeForToken($code);
            $accessToken = (string)($tokenData['access_token'] ?? '');
            if ($accessToken === '') {
                throw new RuntimeException('VK token response has no access_token');
            }

            $profileData = $this->vkId->fetchUserInfo($accessToken);
            $profile = $profileData['user'] ?? $profileData['response'] ?? $profileData;
            $this->auth->loginOrCreateUser($profile, $tokenData);
            $this->logger->info('save_user', 'User saved after callback');
            $this->setFlash('success', 'Вы успешно авторизованы через VK ID.');
        } catch (Throwable $e) {
            $this->logger->error('auth_callback_failed', $e->getMessage());
            $this->setFlash('error', 'Ошибка авторизации: ' . $e->getMessage());
        }

        $this->redirect('/vk-mini/auth');
    }

    public function logout(): void
    {
        $this->auth->logout();
        $this->setFlash('success', 'Вы вышли из VK Mini модуля.');
        $this->redirect('/vk-mini/auth');
    }
}
