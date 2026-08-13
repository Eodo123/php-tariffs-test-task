<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($title ?? 'Тарифы') ?></title>
</head>

<body>

<header>
    <h1>Управление тарифами</h1>
</header>

<main>
    <?= $content ?>
</main>

</body>
</html>
