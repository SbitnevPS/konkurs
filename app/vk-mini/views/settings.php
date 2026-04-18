<?php $mask = static fn(string $v): string => strlen($v) <= 8 ? str_repeat('*', strlen($v)) : substr($v,0,4) . str_repeat('*', strlen($v)-8) . substr($v,-4); ?>
<div class="card">
    <h2>Настройки VK Mini</h2>
    <form method="post" action="/vk-mini/settings/save">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)$csrfToken) ?>">
        <h3>VK ID</h3>
        <?php foreach (['vkid_client_id','vkid_client_secret','vkid_redirect_uri','vkid_authorize_url','vkid_token_url','vkid_userinfo_url','vkid_scope','vkid_api_version'] as $f): ?>
            <label><?= htmlspecialchars($f) ?></label>
            <input type="text" name="<?= htmlspecialchars($f) ?>" value="<?= htmlspecialchars(in_array($f, ['vkid_client_secret'], true) && !empty($settings[$f]) ? $mask((string)$settings[$f]) : (string)($settings[$f] ?? '')) ?>"><br><br>
        <?php endforeach; ?>

        <h3>Публикация</h3>
        <?php foreach (['vk_group_id','vk_group_token','vk_api_version'] as $f): ?>
            <label><?= htmlspecialchars($f) ?></label>
            <input type="text" name="<?= htmlspecialchars($f) ?>" value="<?= htmlspecialchars($f === 'vk_group_token' && !empty($settings[$f]) ? $mask((string)$settings[$f]) : (string)($settings[$f] ?? '')) ?>"><br><br>
        <?php endforeach; ?>
        <label><input type="checkbox" name="vk_publish_from_group" value="1" <?= (($settings['vk_publish_from_group'] ?? '1') === '1') ? 'checked' : '' ?>> vk_publish_from_group</label><br><br>

        <h3>Приложение</h3>
        <?php foreach (['vk_mini_site_domain','vk_mini_module_path','vk_mini_log_entries_limit'] as $f): ?>
            <label><?= htmlspecialchars($f) ?></label>
            <input type="text" name="<?= htmlspecialchars($f) ?>" value="<?= htmlspecialchars((string)($settings[$f] ?? '')) ?>"><br><br>
        <?php endforeach; ?>
        <label><input type="checkbox" name="vk_mini_debug_enabled" value="1" <?= (($settings['vk_mini_debug_enabled'] ?? '0') === '1') ? 'checked' : '' ?>> vk_mini_debug_enabled</label><br><br>

        <button class="btn" type="submit">Сохранить</button>
    </form>
    <p class="muted">Для секретных полей оставьте пусто, чтобы не перезаписывать существующее значение.</p>
</div>
