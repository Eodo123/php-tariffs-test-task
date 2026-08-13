<?php

namespace App\Controller;

use App\Repository\TariffRepository;
use PDOException;
use App\View\View;

class TariffController
{
    public function __construct(
        private TariffRepository $repository,
        private View $view
    ) {
    }

    public function index(): void
    {
        $tariffs = $this->repository->findAll();

        $this->view->render(
            'tariffs/index',
            [
                'title' => 'Тарифы',
                'tariffs' => $tariffs,
            ]
        );
    }

    public function view(int $id): void
    {
        $tariff = $this->repository->findById($id);

        if ($tariff === null) {
            http_response_code(404);
            echo 'Тариф не найден';

            return;
        }

        $this->view->render(
            'tariffs/view',
            [
                'title' => 'Просмотр тарифа',
                'tariff' => $tariff,
            ]
        );
    }

    public function create(): void
    {
        $this->view->render(
            'tariffs/form',
            [
                'title' => 'Создание тарифа',
                'tariff' => null,
                'errors' => [],
            ]
        );
    }

    public function store(): void
    {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $speed = (int) ($_POST['speed'] ?? 0);
        $price = (float) ($_POST['price'] ?? 0);
        $expiresAt = trim($_POST['expires_at'] ?? '');

        $errors = [];

        if ($name === '') {
            $errors[] = 'Название обязательно.';
        }

        if ($speed <= 0) {
            $errors[] = 'Скорость должна быть больше 0.';
        }

        if ($price < 0) {
            $errors[] = 'Стоимость не может быть отрицательной.';
        }

        if (!empty($errors)) {
            $this->view->render(
                'tariffs/form',
                [
                    'title' => 'Создание тарифа',
                    'tariff' => null,
                    'errors' => $errors,
                ]
            );

            return;
        }

        $this->repository->create(
            name: $name,
            description: $description !== '' ? $description : null,
            speed: $speed,
            price: $price,
            createdAt: date('Y-m-d H:i:s'),
            expiresAt: $expiresAt !== ''
                ? date('Y-m-d H:i:s', strtotime($expiresAt))
                : null,
        );

        header('Location: /');
        exit;
    }
}
