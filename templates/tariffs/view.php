<h1>Тариф: <?= htmlspecialchars($tariff->name) ?></h1>

<dl>
    <dt>ID</dt>
    <dd><?= htmlspecialchars((string) $tariff->id) ?></dd>

    <dt>Название</dt>
    <dd><?= htmlspecialchars($tariff->name) ?></dd>

    <dt>Описание</dt>
    <dd><?= htmlspecialchars($tariff->description ?? '') ?></dd>

    <dt>Скорость</dt>
    <dd><?= htmlspecialchars((string) $tariff->speed) ?> Мбит/с</dd>

    <dt>Стоимость</dt>
    <dd><?= htmlspecialchars((string) $tariff->price) ?> ₽</dd>

    <dt>Дата создания</dt>
    <dd><?= htmlspecialchars($tariff->createdAt) ?></dd>

    <dt>Дата окончания</dt>
    <dd><?= htmlspecialchars($tariff->expiresAt ?? '') ?></dd>
</dl>

<a href="/">← Назад к списку</a>
