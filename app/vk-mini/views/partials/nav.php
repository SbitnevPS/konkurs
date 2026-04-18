<nav class="card nav">
    <?php $items = ['/vk-mini/auth' => 'Авторизация', '/vk-mini/publish' => 'Публикация', '/vk-mini/settings' => 'Настройки', '/vk-mini/diagnostics' => 'Диагностика']; ?>
    <?php foreach ($items as $url => $name): ?>
        <a class="<?= str_starts_with((string)$activePath, $url) ? 'active' : '' ?>" href="<?= htmlspecialchars($url) ?>"><?= htmlspecialchars($name) ?></a>
    <?php endforeach; ?>
</nav>
