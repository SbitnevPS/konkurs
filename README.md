# konkurs

## VK Mini module

Новый модуль расположен в `app/vk-mini`.

### Быстрый старт

1. Настройте веб-сервер на `index.php`.
2. Примените SQL `app/vk-mini/storage/migrations/001_create_vk_mini_tables.sql`.
3. Откройте `/vk-mini/settings` и заполните настройки VK ID и VK группы.
4. Для OAuth callback укажите `https://<ваш-домен>/vk-mini/auth/callback`.
5. Протестируйте:
   - `/vk-mini/auth`
   - `/vk-mini/publish`
   - `/vk-mini/diagnostics`
