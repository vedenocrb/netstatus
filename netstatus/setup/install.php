<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Добавляем логирование
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));
error_log("SESSION data: " . print_r($_SESSION, true));

// Проверка на установленный сайт
if (file_exists('../config.php')) {
    $config = require '../config.php';
    if (isset($config['installed']) && $config['installed'] === true) {
        // Сайт уже установлен, показываем уведомление
        ?>
        <!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Система уже установлена</title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body>
            <div class="container">
                <div class="installer-header">
                    <h1>Система мониторинга</h1>
                </div>
                
                <div class="installed-notice">
                    <h2>Система уже установлена</h2>
                    <p>Система мониторинга уже установлена и настроена.</p>
                    <p>Если вы хотите переустановить систему, удалите файл config.php и попробуйте снова.</p>
                    <div class="navigation-buttons">
                        <a href="../index.php" class="btn btn-success">Перейти к системе</a>
                    </div>
                </div>
                <div class="copyright">
                    <div class="copyright-text">Powered by Vedenskaya CRB</div>
                    <div class="version-text">netstatus v. 1.0 (beta)</div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

class Installer {
    private $step = 1;
    private $config = [];
    private $errors = [];
    
    public function __construct() {
        error_log("Constructor called. POST data: " . print_r($_POST, true));
        error_log("GET data: " . print_r($_GET, true));
        
        if (isset($_GET['step'])) {
            $this->step = (int)$_GET['step'];
            error_log("Step set from GET: " . $this->step);
        } else if (isset($_POST['step'])) {
            $this->step = (int)$_POST['step'];
            error_log("Step set from POST: " . $this->step);
        } else {
            $this->step = 1;
            error_log("No step found, defaulting to 1");
        }
        error_log("Final step value: " . $this->step);
    }

    public function run() {
        switch ($this->step) {
            case 1:
                return $this->checkRequirements();
            case 2:
                return $this->getDatabaseInfo();
            case 3:
                return $this->createDatabase();
            default:
                return $this->checkRequirements();
        }
    }

    private function checkRequirements() {
        $requirements = [
            'PHP версия >= 7.0' => version_compare(PHP_VERSION, '7.0.0', '>='),
            'MySQL расширение' => extension_loaded('mysqli'),
            'Права на запись в директорию' => is_writable('../'),
            'cURL расширение' => extension_loaded('curl'),
        ];

        $allMet = true;
        foreach ($requirements as $requirement => $met) {
            if (!$met) {
                $allMet = false;
                $this->errors[] = "Не выполнено требование: $requirement";
            }
        }

        return [
            'template' => 'requirements',
            'data' => [
                'requirements' => $requirements,
                'canContinue' => $allMet
            ]
        ];
    }

    private function getDatabaseInfo() {
        error_log("Starting getDatabaseInfo method");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            error_log("Processing POST request in getDatabaseInfo");
            $this->config = [
                'db_host' => $_POST['db_host'] ?? '',
                'db_name' => $_POST['db_name'] ?? '',
                'db_user' => $_POST['db_user'] ?? '',
                'db_pass' => $_POST['db_pass'] ?? ''
            ];
            error_log("Database config: " . print_r($this->config, true));

            if ($this->testDatabaseConnection()) {
                error_log("Database connection test successful");
                $_SESSION['db_config'] = $this->config;
                error_log("Session updated with db_config");
                header('Location: install.php?step=3');
                exit;
            } else {
                error_log("Database connection test failed. Errors: " . print_r($this->errors, true));
            }
        } else {
            error_log("Not a POST request in getDatabaseInfo");
        }

        return [
            'template' => 'database',
            'data' => [
                'config' => $this->config,
                'errors' => $this->errors
            ]
        ];
    }

    private function createDatabase() {
        if (!isset($_SESSION['db_config'])) {
            header('Location: install.php?step=2');
            exit;
        }

        $config = $_SESSION['db_config'];
        
        // Сначала подключаемся без выбора базы данных
        $mysqli = new mysqli(
            $config['db_host'],
            $config['db_user'],
            $config['db_pass']
        );

        if ($mysqli->connect_error) {
            $this->errors[] = "Ошибка подключения к базе данных: " . $mysqli->connect_error;
            return ['template' => 'error', 'data' => ['errors' => $this->errors]];
        }

        // Создаем базу данных если её нет
        $dbname = $config['db_name'];
        if (!$mysqli->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
            $this->errors[] = "Ошибка создания базы данных: " . $mysqli->error;
            return ['template' => 'error', 'data' => ['errors' => $this->errors]];
        }

        // Выбираем созданную базу данных
        if (!$mysqli->select_db($dbname)) {
            $this->errors[] = "Ошибка выбора базы данных: " . $mysqli->error;
            return ['template' => 'error', 'data' => ['errors' => $this->errors]];
        }

        // SQL для создания таблиц
        $tables = [
            "CREATE TABLE IF NOT EXISTS devices (
                id INT AUTO_INCREMENT PRIMARY KEY,
                device_name VARCHAR(255) NOT NULL,
                device_ip VARCHAR(45) NOT NULL,
                comment TEXT,
                status TINYINT DEFAULT 0,
                last_check TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
            "CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL,
                password VARCHAR(255) NOT NULL,
                is_admin TINYINT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )"
        ];

        foreach ($tables as $sql) {
            if (!$mysqli->query($sql)) {
                $this->errors[] = "Ошибка создания таблицы: " . $mysqli->error;
                return ['template' => 'error', 'data' => ['errors' => $this->errors]];
            }
        }

        // Создание конфигурационного файла
        error_log("Config data before export: " . print_r($config, true));
        
        // Проверяем наличие всех необходимых параметров
        $required = ['db_host', 'db_user', 'db_pass', 'db_name'];
        foreach ($required as $param) {
            if (!isset($config[$param])) {
                $this->errors[] = "Missing required parameter: $param";
                return ['template' => 'error', 'data' => ['errors' => $this->errors]];
            }
        }
        
        $configArray = [
            'db_host' => $config['db_host'],
            'db_user' => $config['db_user'],
            'db_pass' => $config['db_pass'],
            'db_name' => $config['db_name'],
            'installed' => true,
            'install_date' => date('Y-m-d H:i:s')
        ];
        
        $configContent = "<?php\nreturn " . var_export($configArray, true) . ";\n?>";

        error_log("Generated config content: " . $configContent);
        
        if (!file_put_contents('../config.php', $configContent)) {
            $this->errors[] = "Не удалось создать конфигурационный файл";
            return ['template' => 'error', 'data' => ['errors' => $this->errors]];
        }

        // Создание администратора по умолчанию
        $defaultUsername = 'admin';
        $defaultPassword = password_hash('admin', PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, 1)");
        $stmt->bind_param("ss", $defaultUsername, $defaultPassword);
        
        if (!$stmt->execute()) {
            $this->errors[] = "Ошибка создания администратора по умолчанию: " . $mysqli->error;
            return ['template' => 'error', 'data' => ['errors' => $this->errors]];
        }

        return [
            'template' => 'complete',
            'data' => [
                'admin_url' => '../index.php',
                'default_credentials' => [
                    'username' => $defaultUsername,
                    'password' => 'admin'
                ]
            ]
        ];
    }

    private function testDatabaseConnection() {
        try {
            error_log("Attempting to connect to MySQL server...");
            // Сначала пробуем подключиться к MySQL серверу без выбора базы данных
            $mysqli = new mysqli(
                $this->config['db_host'],
                $this->config['db_user'],
                $this->config['db_pass']
            );

            if ($mysqli->connect_error) {
                error_log("MySQL Connection Error: " . $mysqli->connect_error);
                $this->errors[] = "Ошибка подключения к серверу MySQL: " . $mysqli->connect_error;
                return false;
            }

            error_log("Successfully connected to MySQL server");
            // Проверяем существование базы данных
            $dbname = $this->config['db_name'];
            error_log("Checking if database '$dbname' exists...");
            $result = $mysqli->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");

            if ($result->num_rows === 0) {
                // База данных не существует, создаем её
                if (!$mysqli->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
                    error_log("Error creating database: " . $mysqli->error);
                    $this->errors[] = "Ошибка создания базы данных: " . $mysqli->error;
                    return false;
                }
                error_log("Database created successfully");
            }

            // Пробуем подключиться к созданной базе данных
            $mysqli->select_db($dbname);
            if ($mysqli->error) {
                error_log("Error selecting database: " . $mysqli->error);
                $this->errors[] = "Ошибка подключения к базе данных: " . $mysqli->error;
                return false;
            }
            error_log("Successfully selected database");

            $mysqli->close();
            return true;
        } catch (Exception $e) {
            error_log("Error connecting to database: " . $e->getMessage());
            $this->errors[] = "Ошибка подключения к базе данных: " . $e->getMessage();
            return false;
        }
    }
}

$installer = new Installer();
$result = $installer->run();

// Подключаем шаблон
include 'templates/' . $result['template'] . '.php';
?>
