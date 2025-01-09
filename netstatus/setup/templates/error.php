<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Установка системы мониторинга - Ошибка</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="installer-header">
            <h1>Установка системы мониторинга</h1>
        </div>

        <div class="installer-card">
            <h2>Произошла ошибка</h2>
            
            <div class="error-message">
                <?php foreach ($result['data']['errors'] as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>

            <div class="navigation-buttons">
                <a href="javascript:history.back()" class="btn">Вернуться назад</a>
            </div>
        </div>
        <div class="copyright">
            <div class="copyright-text">Powered by Vedenskaya CRB</div>
            <div class="version-text">NetStatus v. 1.0.0 (Beta)</div>
        </div>
    </div>
</body>
</html>
