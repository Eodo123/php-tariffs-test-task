<h1>Тарифы</h1>

<a href="/tariffs/create">
    Создать тариф
</a>

<br>
<br>

<a href="/tariffs/export/csv">
    Экспортировать в CSV
</a>

<br>
<br>

<form
        action="/tariffs/import/csv"
        method="POST"
        enctype="multipart/form-data"
>
    <input
            type="file"
            name="file"
            accept=".csv"
            required
    >

    <button type="submit">
        Импортировать CSV
    </button>
</form>

<?php if (isset($_SESSION['import_result'])): ?>
    <?php $result = $_SESSION['import_result']; ?>

    <div>
        <p>
            Импорт завершён.
            Добавлено: <?= $result['added'] ?>.
            Пропущено: <?= $result['skipped'] ?>.
        </p>

        <?php if ($result['errors']): ?>
            <ul>
                <?php foreach ($result['errors'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <?php unset($_SESSION['import_result']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div>
        <?= htmlspecialchars($_SESSION['error']) ?>
    </div>

    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

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
            <td>
                <input
                        type="number"
                        class="speed-input"
                        data-tariff-id="<?= $tariff->id ?>"
                        value="<?= htmlspecialchars((string) $tariff->speed) ?>"
                        min="1"
                >
                Мбит/с
            </td>
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
<script>
    document.querySelectorAll('.speed-input').forEach(input => {
        input.addEventListener('change', async () => {
            const tariffId = input.dataset.tariffId;
            const speed = input.value;

            try {
                const response = await fetch(
                    `/tariffs/${tariffId}/speed`,
                    {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `speed=${encodeURIComponent(speed)}`,
                    }
                );

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.error || 'Не удалось изменить скорость');
                }

                input.value = data.speed;
            } catch (error) {
                alert(error.message);
            }
        });
    });
</script>
