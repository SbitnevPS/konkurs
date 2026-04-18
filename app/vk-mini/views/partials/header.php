<header class="header">
    <div><strong><?= htmlspecialchars((string)$domain) ?></strong></div>
    <div>
        <?php if (!empty($currentUser)): ?>
            <div class="userbox">
                <img class="avatar" src="<?= htmlspecialchars((string)($currentUser['avatar_url'] ?? '')) ?>" alt="avatar">
                <div>
                    <div><?= htmlspecialchars(trim((string)($currentUser['first_name'] ?? '') . ' ' . ($currentUser['last_name'] ?? ''))) ?></div>
                    <div class="muted"><?= htmlspecialchars((string)($currentUser['email'] ?? '')) ?></div>
                </div>
                <a class="btn secondary" href="/vk-mini/logout">Выйти</a>
            </div>
        <?php else: ?>
            <a class="btn" href="/vk-mini/auth/login">Войти</a>
        <?php endif; ?>
    </div>
</header>
