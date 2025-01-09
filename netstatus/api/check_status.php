<?php
header('Content-Type: application/json');

// Подключаем базу данных
$conn = require_once '../includes/database.php';

function ping($host) {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        exec("ping -n 1 -w 1000 " . escapeshellarg($host), $output, $result);
    } else {
        exec("ping -c 1 -W 1 " . escapeshellarg($host), $output, $result);
    }
    return $result === 0;
}

// Проверяем подключение к базе данных
if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$sql = "SELECT * FROM devices";
$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed: ' . $conn->error]);
    exit;
}

$devices = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $status = ping($row['device_ip']);
        
        // Update last_seen in database
        $currentTime = time();
        if ($status) {
            $updateSql = "UPDATE devices SET last_check = FROM_UNIXTIME($currentTime) WHERE id = " . (int)$row['id'];
            $conn->query($updateSql);
        }
        
        // Преобразуем last_check в timestamp
        $lastCheck = strtotime($row['last_check']);
        
        $devices[] = [
            'id' => $row['id'],
            'device_name' => $row['device_name'],
            'device_ip' => $row['device_ip'],
            'status' => $status,
            'last_seen_time' => $lastCheck ? $lastCheck : null,
            'next_check_seconds' => 10
        ];
    }
}

echo json_encode($devices);
$conn->close();
