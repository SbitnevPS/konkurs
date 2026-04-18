<div class="card">
    <h2>Диагностика VK Mini</h2>
</div>

<div class="card">
    <h3>Блок 1. Авторизация</h3>
    <?php $user = $user ?? null; ?>
    <p>Статус: <strong><?= $user ? 'Авторизован' : 'Не авторизован' ?></strong></p>
    <?php if ($user): ?>
        <p><?= htmlspecialchars((string)$user['first_name']) ?> <?= htmlspecialchars((string)$user['last_name']) ?>, <?= htmlspecialchars((string)$user['email']) ?>, VK ID <?= htmlspecialchars((string)$user['vk_user_id']) ?></p>
        <p>Access token: <?= !empty($user['access_token']) ? 'есть' : 'нет' ?>, Refresh token: <?= !empty($user['refresh_token']) ? 'есть' : 'нет' ?>, expires: <?= htmlspecialchars((string)($user['token_expires_at'] ?? '-')) ?></p>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Блок 2. Настройки публикации</h3>
    <p>group_id: <?= htmlspecialchars((string)($settings['vk_group_id'] ?? '-')) ?></p>
    <p>token: <?= !empty($settings['vk_group_token']) ? 'задан' : 'не задан' ?></p>
    <p>from_group: <?= (($settings['vk_publish_from_group'] ?? '1') === '1') ? 'включён' : 'выключен' ?></p>
    <p>API version: <?= htmlspecialchars((string)($settings['vk_api_version'] ?? ($settings['vkid_api_version'] ?? '5.199'))) ?></p>
    <button class="btn" type="button" id="test-token-btn">Проверить ключ сообщества</button>
    <pre id="test-token-result">Нажмите кнопку для теста.</pre>
</div>

<div class="card">
    <h3>Блок 3. Последняя публикация</h3>
    <?php if ($lastPost): ?>
        <p>Время: <?= htmlspecialchars((string)$lastPost['created_at']) ?></p>
        <p>Текст: <?= htmlspecialchars((string)$lastPost['post_text']) ?></p>
        <p>Файл: <?= htmlspecialchars((string)$lastPost['local_image_path']) ?></p>
        <p>Статус: <?= htmlspecialchars((string)$lastPost['status']) ?></p>
        <p>vk_post_id: <?= htmlspecialchars((string)$lastPost['vk_post_id']) ?></p>
        <pre><?= htmlspecialchars((string)$lastPost['vk_response_json']) ?></pre>
    <?php else: ?>
        <p>Публикаций пока нет.</p>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/partials/diagnostics-log-table.php'; ?>

<script>
document.getElementById('test-token-btn')?.addEventListener('click', async () => {
    const resultEl = document.getElementById('test-token-result');
    resultEl.textContent = 'Проверка...';
    try {
        const response = await fetch('/vk-mini/api/diagnostics/test-group-token');
        const json = await response.json();
        resultEl.textContent = JSON.stringify(json, null, 2);
    } catch (e) {
        resultEl.textContent = 'Ошибка: ' + e.message;
    }
});
</script>
