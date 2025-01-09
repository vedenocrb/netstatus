<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Установка системы мониторинга - Завершение</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        @keyframes blink {
            0% { opacity: 1; }
            50% { opacity: 0.3; }
            100% { opacity: 1; }
        }
        .blinking-warning {
            color: white;
            animation: blink 2s infinite;
            font-weight: bold;
        }
    </style>
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
            <div class="step completed">
                <div class="step-number">3</div>
                <div class="step-label">Завершение</div>
            </div>
        </div>

        <div class="installer-card">
            <h2>Установка завершена</h2>
            
            <div class="success-message">
                <p>Поздравляем! Система мониторинга успешно установлена.</p>
                <p class="blinking-warning">Внимание! После установки настоятельно рекомендуется удалить папку "setup" для обеспечения безопасности системы.</p>
            </div>

            <div class="navigation-buttons" style="text-align: center;">
                <a href="<?php echo htmlspecialchars($result['data']['admin_url']); ?>" class="btn btn-success" style="display: inline-block; margin: 0 auto;">Перейти к системе</a>
            </div>
        </div>

        <div class="copyright">
            <div class="copyright-text">Powered by Vedenskaya CRB</div>
            <div class="version-text">NetStatus v. 1.0.0 (Beta)</div>
        </div>
    </div>
</body>
</html>
