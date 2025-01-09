<?php
require_once 'config.php';
require_once 'lang/Language.php';
$lang = Language::getInstance();

// Database setup logic
$success_messages = [];
$error_messages = [];

try {
    // Create connection
    $conn = new mysqli($db_host, $db_user, $db_password);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception($lang->get('setup.connection_failed') . ": " . $conn->connect_error);
    }

    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS device_monitor";
    if ($conn->query($sql) === TRUE) {
        $success_messages[] = $lang->get('setup.db_created');
    } else {
        throw new Exception($lang->get('setup.error_db') . ": " . $conn->error);
    }

    // Select the database
    $conn->select_db("device_monitor");

    // Create devices table
    $sql = "CREATE TABLE IF NOT EXISTS devices (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        device_name VARCHAR(255) NOT NULL,
        device_ip VARCHAR(255) NOT NULL,
        comment TEXT,
        last_seen INT(11) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql) === TRUE) {
        $success_messages[] = $lang->get('setup.table_created');
    } else {
        throw new Exception($lang->get('setup.error_table') . ": " . $conn->error);
    }

    // Add comment column if it doesn't exist
    $sql = "SHOW COLUMNS FROM devices LIKE 'comment'";
    $result = $conn->query($sql);
    if ($result->num_rows === 0) {
        $sql = "ALTER TABLE devices ADD COLUMN comment TEXT AFTER device_ip";
        if ($conn->query($sql) === TRUE) {
            $success_messages[] = $lang->get('setup.comment_added');
        } else {
            throw new Exception($lang->get('setup.error_comment') . ": " . $conn->error);
        }
    }

} catch (Exception $e) {
    $error_messages[] = $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang->get('setup.title'); ?> - <?php echo $lang->get('title'); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .setup-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #2a2a2a;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .setup-title {
            text-align: center;
            margin-bottom: 30px;
            color: #fff;
        }
        .message {
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 4px;
            font-family: monospace;
        }
        .success {
            background-color: #1a472a;
            color: #2bff88;
            border: 1px solid #2bff88;
        }
        .error {
            background-color: #4a1f1f;
            color: #ff4444;
            border: 1px solid #ff4444;
        }
        .setup-actions {
            margin-top: 30px;
            text-align: center;
        }
        .setup-actions a {
            display: inline-block;
            padding: 4px 10px;
            margin-left: 8px;
            background-color: #2a2a2a;
            color: #666;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 1px solid #444;
            font-size: 0.9em;
        }
        .setup-actions a:hover {
            background-color: #333;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="setup-container">
            <h1 class="setup-title"><?php echo $lang->get('setup.title'); ?></h1>
            
            <?php if (!empty($success_messages)): ?>
                <?php foreach ($success_messages as $message): ?>
                    <div class="message success">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($error_messages)): ?>
                <?php foreach ($error_messages as $message): ?>
                    <div class="message error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="setup-actions">
                <a href="index.php">
                    <i class="fas fa-home"></i> <?php echo $lang->get('setup.return_home'); ?>
                </a>
                <a href="setup_database.php">
                    <i class="fas fa-sync"></i> <?php echo $lang->get('setup.run_again'); ?>
                </a>
            </div>
        </div>
    </div>
</body>
</html>
