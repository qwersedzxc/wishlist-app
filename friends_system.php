<?php


try {
    $db = new PDO(
        "pgsql:host=localhost;port=5432;dbname=wishlist_service",
        "postgres",
        "4321",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "=== Обновление базы данных ===\n\n";
    
    // тип для статуса дружбы
    echo "Создание типа friend_status_enum...\n";
    $db->exec("DROP TYPE IF EXISTS friend_status_enum CASCADE");
    $db->exec("CREATE TYPE friend_status_enum AS ENUM ('pending', 'accepted', 'rejected')");
    
    // таблица запросов в друзья
    echo "Создание таблицы friend_requests...\n";
    $db->exec("DROP TABLE IF EXISTS friend_requests CASCADE");
    $db->exec("
        CREATE TABLE friend_requests (
            id SERIAL PRIMARY KEY,
            sender_id INTEGER NOT NULL,
            receiver_id INTEGER NOT NULL,
            status friend_status_enum DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE(sender_id, receiver_id)
        )
    ");
    
    //  индексы
    echo "Создание индексов...\n";
    $db->exec("CREATE INDEX idx_friend_requests_sender ON friend_requests(sender_id)");
    $db->exec("CREATE INDEX idx_friend_requests_receiver ON friend_requests(receiver_id)");
    $db->exec("CREATE INDEX idx_friend_requests_status ON friend_requests(status)");
    
    //  тип приватности вишлистов
    echo "Обновление типа privacy_enum...\n";
    $db->exec("DROP TYPE IF EXISTS privacy_enum CASCADE");
    $db->exec("CREATE TYPE privacy_enum AS ENUM ('public', 'friends', 'link')");
    
    //  колонки в wishlists
    echo "Обновление таблицы wishlists...\n";
    $db->exec("ALTER TABLE wishlists DROP COLUMN IF EXISTS privacy CASCADE");
    $db->exec("ALTER TABLE wishlists DROP COLUMN IF EXISTS share_token CASCADE");
    $db->exec("ALTER TABLE wishlists ADD COLUMN privacy privacy_enum DEFAULT 'public'");
    $db->exec("ALTER TABLE wishlists ADD COLUMN share_token VARCHAR(64) UNIQUE");
    
    // существующие записи
    echo "Обновление существующих вишлистов...\n";
    $db->exec("UPDATE wishlists SET privacy = CASE WHEN is_public = TRUE THEN 'public'::privacy_enum ELSE 'friends'::privacy_enum END");
    
    echo "\n✓ База данных успешно обновлена!\n";
    echo "\nДобавлено:\n";
    echo "  - Таблица friend_requests для системы друзей\n";
    echo "  - Колонка privacy (public/friends/link) в wishlists\n";
    echo "  - Колонка share_token для доступа по ссылке\n";
    
} catch (Exception $e) {
    echo "✗ Ошибка: " . $e->getMessage() . "\n";
}
