<div class="card">
    <h2>Публикация в сообщество VK</h2>
    <form method="post" action="/vk-mini/publish/send" enctype="multipart/form-data">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)$csrfToken) ?>">
        <label>Текст публикации</label>
        <textarea name="post_text" rows="4" placeholder="Введите текст"></textarea><br><br>
        <label>Фото (jpg/jpeg/png/webp, до 10MB)</label>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"><br><br>
        <button class="btn" type="submit">Опубликовать</button>
    </form>
</div>
