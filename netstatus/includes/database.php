<?php
// Загружаем конфигурацию
$config = @require __DIR__ . '/../config.php';

error_log("Loaded config in database.php: " . print_r($config, true));

// Проверяем, что конфигурация является массивом
if (!is_array($config)) {
    error_log("Config is not an array. Received: " . gettype($config));
    die("Invalid configuration format");
}

// Проверяем наличие всех необходимых параметров
$required_params = ['db_host', 'db_user', 'db_name', 'db_pass'];
$missing_params = [];

foreach ($required_params as $param) {
    if (!isset($config[$param])) {
        $missing_params[] = $param;
    }
}

if (!empty($missing_params)) {
    error_log("Missing parameters: " . implode(", ", $missing_params));
    error_log("Available config: " . print_r($config, true));
    die("Missing database configuration parameters: " . implode(", ", $missing_params));
}

// Создаем подключение к базе данных
try {
    error_log("Attempting to connect with: host={$config['db_host']}, user={$config['db_user']}, dbname={$config['db_name']}");
    
    $conn = mysqli_connect(
        $config['db_host'],
        $config['db_user'],
        $config['db_pass'],
        $config['db_name']
    );

    // Проверяем подключение
    if (!$conn) {
        error_log("Connection failed: " . mysqli_connect_error());
        die("Connection failed: " . mysqli_connect_error());
    }

    error_log("Database connection successful");
    
    // Устанавливаем кодировку
    mysqli_set_charset($conn, "utf8mb4");

    return $conn;
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Database connection error: " . $e->getMessage());
}
