<h1>Тариф: <?= htmlspecialchars($tariff->name) ?></h1>
<div class="card">
    <dl class="dl">
        <dt class="dt">ID</dt>
        <dd class="dd"><?= htmlspecialchars((string) $tariff->id) ?></dd>

        <dt class="dt">Название</dt>
        <dd class="dd"><?= htmlspecialchars($tariff->name) ?></dd>

        <dt class="dt">Описание</dt>
        <dd class="dd"><?= htmlspecialchars($tariff->description ?? '') ?></dd>

        <dt class="dt">Скорость</dt>
        <dd class="dd"><?= htmlspecialchars((string) $tariff->speed) ?> Мбит/с</dd>

        <dt class="dt">Стоимость</dt>
        <dd class="dd"><?= htmlspecialchars((string) $tariff->price) ?> ₽</dd>

        <dt class="dt">Дата создания</dt>
        <dd class="dd"><?= htmlspecialchars($tariff->createdAt) ?></dd>

        <dt class="dt">Дата окончания</dt>
        <dd class="dd"><?= htmlspecialchars($tariff->expiresAt ?? '') ?></dd>
    </dl>
</div>
<a class="btn btn-primary" href="/tariffs/<?= $tariff->id ?>/edit">
    Редактировать
</a>

<br>
<br>

<a class="btn btn-outline"  href="/">
    ← Назад к списку
</a>
