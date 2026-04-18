<div class="card">
    <?php if (!empty($user)): ?>
        <div class="userbox">
            <img class="avatar" src="<?= htmlspecialchars((string)($user['avatar_url'] ?? '')) ?>" alt="avatar">
            <div>
                <strong><?= htmlspecialchars(trim((string)($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?></strong><br>
                <span class="muted">Email: <?= htmlspecialchars((string)($user['email'] ?? '-')) ?></span><br>
                <span class="muted">VK ID: <?= htmlspecialchars((string)($user['vk_user_id'] ?? '-')) ?></span><br>
                <span class="muted">Вход: <?= htmlspecialchars((string)($user['login_at'] ?? '-')) ?></span><br>
                <span class="muted">Токен до: <?= htmlspecialchars((string)($user['token_expires_at'] ?? '-')) ?></span>
            </div>
        </div>
    <?php else: ?>
        <p>Пользователь не авторизован.</p>
    <?php endif; ?>
</div>
