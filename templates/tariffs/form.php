<h1><?= htmlspecialchars($title) ?></h1>

<?php if (!empty($errors)): ?>
    <div>
        <strong>Ошибки:</strong>

        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form
        method="POST"
        action="<?= $tariff !== null
                ? '/tariffs/' . $tariff->id
                : '/tariffs' ?>"
>

    <div>
        <label for="name">Название:</label>

        <input
            type="text"
            id="name"
            name="name"
            value="<?= htmlspecialchars($tariff?->name ?? '') ?>"
            required
        >
    </div>

    <br>

    <div>
        <label for="description">Описание:</label>

        <textarea
            id="description"
            name="description"
        ><?= htmlspecialchars($tariff?->description ?? '') ?></textarea>
    </div>

    <br>

    <div>
        <label for="speed">Скорость:</label>

        <input
            type="number"
            id="speed"
            name="speed"
            value="<?= htmlspecialchars((string) ($tariff?->speed ?? '')) ?>"
            min="1"
            required
        >

        Мбит/с
    </div>

    <br>

    <div>
        <label for="price">Стоимость:</label>

        <input
            type="number"
            id="price"
            name="price"
            value="<?= htmlspecialchars((string) ($tariff?->price ?? '')) ?>"
            min="0"
            step="0.01"
            required
        >

        ₽
    </div>

    <br>

    <div>
        <label for="expires_at">Дата окончания:</label>

        <input
            type="datetime-local"
            id="expires_at"
            name="expires_at"
            value="<?= htmlspecialchars(
                $tariff?->expiresAt
                    ? date('Y-m-d\TH:i', strtotime($tariff->expiresAt))
                    : ''
            ) ?>"
        >
    </div>

    <br>

    <button type="submit">
        Сохранить
    </button>

</form>

<br>

<a href="/">← Назад к списку</a>
