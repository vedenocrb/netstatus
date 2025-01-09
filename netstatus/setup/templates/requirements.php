<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Установка системы мониторинга - Проверка требований</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="installer-header">
            <h1>Установка системы мониторинга</h1>
        </div>
        
        <div class="step-indicator">
            <div class="step active">
                <div class="step-number">1</div>
                <div class="step-label">Требования</div>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-label">База данных</div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-label">Завершение</div>
            </div>
        </div>

        <div class="installer-card">
            <h2>Проверка системных требований</h2>
            
            <ul class="requirements-list">
                <?php foreach ($result['data']['requirements'] as $requirement => $met): ?>
                <li>
                    <?php echo htmlspecialchars($requirement); ?>
                    <span class="status-icon <?php echo $met ? 'success' : 'error'; ?>"></span>
                </li>
                <?php endforeach; ?>
            </ul>

            <div class="navigation-buttons">
                <?php if ($result['data']['canContinue']): ?>
                <form action="install.php" method="post">
                    <input type="hidden" name="step" value="2">
                    <button type="submit" class="btn btn-success">Продолжить</button>
                </form>
                <?php else: ?>
                <div class="error-message">
                    Пожалуйста, исправьте все ошибки перед продолжением установки.
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="copyright">
            <div class="copyright-text">Powered by Vedenskaya CRB</div>
            <div class="version-text">NetStatus v. 1.0.0 (Beta)</div>
        </div>
    </div>
</body>
</html>
