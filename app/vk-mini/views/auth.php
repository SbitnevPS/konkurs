<div class="card">
    <h2>Авторизация через VK ID</h2>
    <?php if (!empty($currentUser)): ?>
        <p>Вы авторизованы.</p>
        <?php $user = $currentUser; require __DIR__ . '/partials/user-card.php'; ?>
        <a class="btn secondary" href="/vk-mini/logout">Выйти</a>
    <?php else: ?>
        <p>Для продолжения войдите через VK ID.</p>
        <a class="btn" href="/vk-mini/auth/login">Войти через VK ID</a>
    <?php endif; ?>
</div>
