<?php


try {
    $db = new PDO(
        "pgsql:host=localhost;port=5432;dbname=wishlist_service",
        "postgres",
        "4321",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    $db->exec("ALTER TABLE wishlists ADD COLUMN IF NOT EXISTS cover_image VARCHAR(255)");
    echo "Колонка cover_image успешно добавлена!\n";
    
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}
