<h1><?= htmlspecialchars($title) ?></h1>

<?php if (!empty($errors)): ?>
    <div class="alert">
        <strong>Ошибки:</strong>

        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<div class="card">
<form
        method="POST"
        action="<?= $tariff !== null
                ? '/tariffs/' . $tariff->id
                : '/tariffs' ?>"
>

    <div class="form-group">
        <label class="label" for="name">Название:</label>

        <input
            type="text"
            id="name"
            name="name"
            value="<?= htmlspecialchars($tariff?->name ?? '') ?>"
            required
        >
    </div>

    <br>

    <div class="form-group">
        <label class="label" for="description">Описание:</label>

        <textarea
            id="description"
            name="description"
        ><?= htmlspecialchars($tariff?->description ?? '') ?></textarea>
    </div>

    <br>

    <div class="form-group">
        <label class="label" for="speed">Скорость:</label>

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

    <div class="form-group">
        <label class="label" for="price">Стоимость:</label>

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

    <div class="form-group">
        <label class="label" for="expires_at">Дата окончания:</label>

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

    <button class="btn btn-primary" type="submit">
        Сохранить
    </button>

</form>
</div>
<br>

<a class="btn btn-outline" href="/">← Назад к списку</a>
