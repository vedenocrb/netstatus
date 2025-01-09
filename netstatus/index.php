<?php
session_start();

// Проверяем существование config.php
if (!file_exists(__DIR__ . '/config.php')) {
    header('Location: setup/install.php');
    exit;
}

// Проверяем валидность конфигурации
$config = @require __DIR__ . '/config.php';
if (!is_array($config) || !isset($config['installed']) || $config['installed'] !== true) {
    header('Location: setup/install.php');
    exit;
}

require_once 'lang/Language.php';

// Подключаемся к базе данных
$conn = require_once 'includes/database.php';

// Handle AJAX language change
if (isset($_POST['action']) && $_POST['action'] === 'change_language') {
    $lang = Language::getInstance();
    if ($lang->setLanguage($_POST['lang'])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

$lang = Language::getInstance();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang->getCurrentLang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang->get('title'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="container">
        <?php echo $lang->getLanguageSelector(); ?>
        <div class="grid-container">
            <?php            
            // Get all devices from database
            $sql = "SELECT * FROM devices ORDER BY id ASC";
            $result = $conn->query($sql);
            $devices = [];

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $devices[] = $row;
                }
            }

            // Display existing devices
            foreach ($devices as $row) {
                echo '<div class="device-card" data-id="' . $row['id'] . '">';
                echo '<div class="action-icons">';
                echo '<i class="fas fa-edit edit-device" data-id="' . $row['id'] . '" title="' . $lang->get('edit_device') . '"></i>';
                echo '<i class="fas fa-trash-alt remove-device" data-id="' . $row['id'] . '" title="' . $lang->get('delete_device') . '"></i>';
                echo '</div>';
                echo '<h2 class="device-name">' . htmlspecialchars($row['device_name']) . '</h2>';
                echo '<p class="device-ip">' . htmlspecialchars($row['device_ip']) . '</p>';
                echo '<p class="device-comment">' . htmlspecialchars($row['comment'] ?? '') . '</p>';
                echo '<div class="status-indicator">' . $lang->get('status') . '...</div>';
                echo '<p class="last-seen">' . $lang->get('last_seen') . ': -- sec.</p>';
                echo '</div>';
            }
            
            // Add just one "Add Device" card
            echo '<div class="device-card empty">';
            echo '    <i class="fa-solid fa-circle-plus fa-2x"></i>';
            echo '    <p>' . $lang->get('add_device') . '</p>';
            echo '</div>';
            ?>
        </div>
    </div>

    <!-- Add Device Modal -->
    <div id="addDeviceModal" class="modal">
        <div class="modal-content">
            <h2><?php echo $lang->get('add_device'); ?></h2>
            <form id="addDeviceForm">
                <input type="text" id="deviceName" placeholder="<?php echo $lang->get('device_name'); ?>" required>
                <input type="text" id="deviceIP" placeholder="<?php echo $lang->get('device_ip'); ?>" required>
                <textarea id="deviceComment" placeholder="<?php echo $lang->get('comment'); ?>"></textarea>
                <div class="button-container">
                    <button type="submit"><?php echo $lang->get('save'); ?></button>
                    <button type="button" class="cancel"><?php echo $lang->get('cancel'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Device Modal -->
    <div id="editDeviceModal" class="modal">
        <div class="modal-content">
            <h2><?php echo $lang->get('edit_device'); ?></h2>
            <form id="editDeviceForm">
                <input type="hidden" id="editDeviceId">
                <input type="text" id="editDeviceName" placeholder="<?php echo $lang->get('device_name'); ?>" required>
                <input type="text" id="editDeviceIP" placeholder="<?php echo $lang->get('device_ip'); ?>" required>
                <textarea id="editDeviceComment" placeholder="<?php echo $lang->get('comment'); ?>"></textarea>
                <div class="button-container">
                    <button type="submit"><?php echo $lang->get('save'); ?></button>
                    <button type="button" class="cancel"><?php echo $lang->get('cancel'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>Powered by Vedenskaya CRB</p>
    </footer>

    <audio id="connectedSound" src="sounds/connected.mp3" preload="auto"></audio>
    <audio id="disconnectedSound" src="sounds/disconnected.mp3" preload="auto"></audio>
    <audio id="alertSound" src="sounds/alert.mp3" preload="auto"></audio>

    <script>
        // Pass translations to JavaScript
        window.translations = <?php echo json_encode([
            'confirm_delete' => $lang->get('confirm_delete'),
            'yes' => $lang->get('yes'),
            'no' => $lang->get('no'),
            'error' => [
                'invalid_ip' => $lang->get('error.invalid_ip'),
                'required_fields' => $lang->get('error.required_fields'),
                'server_error' => $lang->get('error.server_error')
            ]
        ]); ?>;
    </script>
    <script>
        // Add translations to window object
        window.translations = {
            ...window.translations,
            lost_device: '<?php echo $lang->get('lost_device'); ?>',
            next_check: '<?php echo $lang->get('next_check'); ?>',
            connected: '<?php echo $lang->get('connected'); ?>',
            disconnected: '<?php echo $lang->get('disconnected'); ?>'
        };
    </script>
    <script src="js/script.js"></script>
</body>
</html>
