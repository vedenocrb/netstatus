<?php
header('Content-Type: application/json');

// Подключаем базу данных
$conn = require_once '../includes/database.php';

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if the data is valid
if ($data === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON data']);
    exit;
}

// Check if the connection is successful
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

if (isset($data['deviceId'], $data['deviceName'], $data['deviceIP'])) {
    $deviceId = (int)$data['deviceId'];
    $deviceName = $conn->real_escape_string($data['deviceName']);
    $deviceIP = $conn->real_escape_string($data['deviceIP']);
    $comment = isset($data['deviceComment']) ? $conn->real_escape_string($data['deviceComment']) : '';

    $sql = "UPDATE devices SET 
            device_name = '$deviceName', 
            device_ip = '$deviceIP', 
            comment = '$comment' 
            WHERE id = $deviceId";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $conn->error]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
}

$conn->close();
?>
