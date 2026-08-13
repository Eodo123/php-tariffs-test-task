<h1>Тарифы</h1>

<a href="/tariffs/create">
    Создать тариф
</a>

<br>
<br>

<table border="1">
    <thead>
    <tr>
        <th>ID</th>
        <th>Название</th>
        <th>Описание</th>
        <th>Скорость</th>
        <th>Стоимость</th>
        <th>Дата создания</th>
        <th>Дата окончания</th>
        <th>Действия</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($tariffs as $tariff): ?>
        <tr>
            <td><?= htmlspecialchars((string) $tariff->id) ?></td>
            <td><?= htmlspecialchars($tariff->name) ?></td>
            <td><?= htmlspecialchars($tariff->description ?? '') ?></td>
            <td><?= htmlspecialchars((string) $tariff->speed) ?> Мбит/с</td>
            <td><?= htmlspecialchars((string) $tariff->price) ?> ₽</td>
            <td><?= htmlspecialchars($tariff->createdAt) ?></td>
            <td><?= htmlspecialchars($tariff->expiresAt ?? '') ?></td>
            <td>
                <a href="/tariffs/<?= $tariff->id ?>">
                    Просмотр
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
