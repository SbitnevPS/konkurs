<?php if (!empty($flash)): ?>
    <div class="alert <?= htmlspecialchars((string)$flash['type']) ?>">
        <?= htmlspecialchars((string)$flash['message']) ?>
    </div>
<?php endif; ?>
