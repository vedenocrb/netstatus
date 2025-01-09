<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Установка системы мониторинга - Создание администратора</title>
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
            <div class="step completed">
                <div class="step-number">2</div>
                <div class="step-label">База данных</div>
            </div>
            <div class="step active">
                <div class="step-number">3</div>
                <div class="step-label">Администратор</div>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <div class="step-label">Завершение</div>
            </div>
        </div>

        <div class="installer-card">
            <h2>Создание учетной записи администратора</h2>
            
            <?php if (!empty($result['data']['errors'])): ?>
            <div class="error-message">
                <?php foreach ($result['data']['errors'] as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form action="install.php" method="post">
                <input type="hidden" name="step" value="4">
                
                <div class="form-group">
                    <label for="admin_username">Имя пользователя:</label>
                    <input type="text" id="admin_username" name="admin_username" required minlength="4">
                </div>

                <div class="form-group">
                    <label for="admin_password">Пароль:</label>
                    <input type="password" id="admin_password" name="admin_password" required minlength="6">
                </div>

                <div class="navigation-buttons">
                    <button type="submit" class="btn btn-success">Создать администратора</button>
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
