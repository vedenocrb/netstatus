<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Установка системы мониторинга - Настройка базы данных</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="installer-header">
            <h1>Установка системы мониторинга</h1>
        </div>
        
        <div class="step-indicator">
            <div class="step completed">
                <div class="step-number">1</div>
                <div class="step-label">Требования</div>
            </div>
            <div class="step active">
                <div class="step-number">2</div>
                <div class="step-label">База данных</div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-label">Завершение</div>
            </div>
        </div>

        <div class="installer-card">
            <h2>Настройка базы данных</h2>
            
            <?php if (!empty($result['data']['errors'])): ?>
            <div class="error-message">
                <?php foreach ($result['data']['errors'] as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="xampp-notice">
                <p>Для установки на XAMPP используйте следующие настройки по умолчанию:</p>
                <ul>
                    <li>Хост: localhost</li>
                    <li>Пользователь: root</li>
                    <li>Пароль: оставьте пустым</li>
                </ul>
            </div>

            <form action="install.php" method="post">
                <input type="hidden" name="step" value="2">
                
                <div class="form-group">
                    <label for="db_host">Хост базы данных:</label>
                    <input type="text" id="db_host" name="db_host" value="<?php echo htmlspecialchars($result['data']['config']['db_host'] ?? 'localhost'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="db_name">Имя базы данных:</label>
                    <input type="text" id="db_name" name="db_name" value="<?php echo htmlspecialchars($result['data']['config']['db_name'] ?? 'netstatus'); ?>" required>
                    <div class="field-hint">База данных будет создана автоматически, если она не существует</div>
                </div>

                <div class="form-group">
                    <label for="db_user">Пользователь базы данных:</label>
                    <input type="text" id="db_user" name="db_user" value="<?php echo htmlspecialchars($result['data']['config']['db_user'] ?? 'root'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="db_pass">Пароль базы данных:</label>
                    <input type="password" id="db_pass" name="db_pass" value="<?php echo htmlspecialchars($result['data']['config']['db_pass'] ?? ''); ?>">
                    <div class="field-hint">Для XAMPP оставьте поле пустым</div>
                </div>

                <div class="navigation-buttons">
                    <button type="submit" class="btn btn-success">Продолжить</button>
                </div>
            </form>
        </div>

        <div class="copyright">
            <div class="copyright-text">Powered by Vedenskaya CRB</div>
            <div class="version-text">NetStatus v. 1.0.0 (Beta)</div>
        </div>
    </div>
</body>
</html>
