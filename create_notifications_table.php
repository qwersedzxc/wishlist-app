<?php
// Create notifications table

try {
    $pdo = new PDO('pgsql:host=localhost;port=5432;dbname=wishlist_service', 'postgres', '4321');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Creating notifications table...\n";
    
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS notifications (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL,
            type VARCHAR(50) NOT NULL,
            title VARCHAR(200) NOT NULL,
            message TEXT,
            link VARCHAR(500),
            from_user_id INTEGER,
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE SET NULL
        )
    ");
    
    echo "Creating indexes...\n";
    
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_notifications_user ON notifications(user_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_notifications_read ON notifications(is_read)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_notifications_created ON notifications(created_at)");
    
    echo "✓ Notifications table created successfully\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
