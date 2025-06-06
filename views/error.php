<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"/>
    <title>Ошибка</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f8d7da;
            color: #721c24;
        }

        .error-message {
            border: 1px solid #f5c6cb;
            background: #f8d7da;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<h1>Ошибка</h1>
<div class="error-message">
    <?= htmlspecialchars($message ?? 'Произошла ошибка') ?>
</div>
</body>
</html>
