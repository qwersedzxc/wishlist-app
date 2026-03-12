<?php
/**
 * Health Check Script
 * Проверяет готовность приложения к работе
 */

header('Content-Type: application/json');

$checks = [
    'database' => false,
    'uploads_dir' => false,
    'php_version' => false,
    'pdo_pgsql' => false,
];

$errors = [];

// Проверка версии PHP
if (version_compare(PHP_VERSION, '8.0.0', '>=')) {
    $checks['php_version'] = true;
} else {
    $errors[] = 'PHP version must be 8.0 or higher. Current: ' . PHP_VERSION;
}

// Проверка расширения PDO PostgreSQL
if (extension_loaded('pdo_pgsql')) {
    $checks['pdo_pgsql'] = true;
} else {
    $errors[] = 'PDO PostgreSQL extension is not loaded';
}

// Проверка подключения к базе данных
try {
    require_once __DIR__ . '/config/db.php';
    $db = Database::getInstance()->getConnection();
    
    // Проверка наличия таблиц
    $stmt = $db->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'public'");
    $tableCount = $stmt->fetchColumn();
    
    if ($tableCount > 0) {
        $checks['database'] = true;
    } else {
        $errors[] = 'Database is empty. Run migrations first.';
    }
} catch (Exception $e) {
    $errors[] = 'Database connection failed: ' . $e->getMessage();
}

// Проверка директории uploads
$uploadsDir = __DIR__ . '/public/uploads';
if (is_dir($uploadsDir) && is_writable($uploadsDir)) {
    $checks['uploads_dir'] = true;
} else {
    $errors[] = 'Uploads directory is not writable: ' . $uploadsDir;
}

// Результат
$allChecks = array_reduce($checks, function($carry, $item) {
    return $carry && $item;
}, true);

$response = [
    'status' => $allChecks ? 'healthy' : 'unhealthy',
    'checks' => $checks,
    'errors' => $errors,
    'timestamp' => date('Y-m-d H:i:s'),
];

http_response_code($allChecks ? 200 : 503);
echo json_encode($response, JSON_PRETTY_PRINT);
