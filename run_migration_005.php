<?php


try {
    $pdo = new PDO('pgsql:host=localhost;port=5432;dbname=wishlist_service', 'postgres', '4321');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = file_get_contents('migrations/005_add_notifications.sql');
    $pdo->exec($sql);
    
    echo "✓ Migration 005 executed successfully\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
