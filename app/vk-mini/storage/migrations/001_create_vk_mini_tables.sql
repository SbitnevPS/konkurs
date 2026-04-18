CREATE TABLE IF NOT EXISTS vk_mini_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vk_user_id VARCHAR(64) NOT NULL UNIQUE,
    first_name VARCHAR(255) DEFAULT NULL,
    last_name VARCHAR(255) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    avatar_url TEXT DEFAULT NULL,
    access_token TEXT DEFAULT NULL,
    refresh_token TEXT DEFAULT NULL,
    token_expires_at DATETIME DEFAULT NULL,
    raw_profile_json LONGTEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS vk_mini_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(191) NOT NULL UNIQUE,
    setting_value LONGTEXT DEFAULT NULL,
    updated_at DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS vk_mini_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    group_id VARCHAR(64) DEFAULT NULL,
    post_text LONGTEXT DEFAULT NULL,
    local_image_path TEXT DEFAULT NULL,
    vk_photo_id VARCHAR(255) DEFAULT NULL,
    vk_owner_id VARCHAR(255) DEFAULT NULL,
    vk_post_id VARCHAR(255) DEFAULT NULL,
    vk_response_json LONGTEXT DEFAULT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    error_message LONGTEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_vk_mini_posts_user_id (user_id),
    INDEX idx_vk_mini_posts_group_id (group_id)
);

CREATE TABLE IF NOT EXISTS vk_mini_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level VARCHAR(20) NOT NULL,
    channel VARCHAR(100) DEFAULT NULL,
    action VARCHAR(100) DEFAULT NULL,
    message TEXT DEFAULT NULL,
    request_url TEXT DEFAULT NULL,
    request_method VARCHAR(20) DEFAULT NULL,
    request_payload LONGTEXT DEFAULT NULL,
    response_code INT DEFAULT NULL,
    response_payload LONGTEXT DEFAULT NULL,
    context_json LONGTEXT DEFAULT NULL,
    created_at DATETIME NOT NULL,
    INDEX idx_vk_mini_logs_level (level),
    INDEX idx_vk_mini_logs_action (action),
    INDEX idx_vk_mini_logs_created_at (created_at)
);
