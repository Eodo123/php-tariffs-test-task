<?php

namespace App\Controller;

use App\Repository\TariffRepository;
use PDOException;
use App\Model\Tariff;
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

        $tariff = new Tariff(
            id: null,
            name: $name,
            description: $description !== '' ? $description : null,
            speed: $speed,
            price: $price,
            createdAt: '',
            expiresAt: $expiresAt !== ''
                ? date('Y-m-d H:i:s', strtotime($expiresAt))
                : null,
        );

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
                    'tariff' => $tariff,
                    'errors' => $errors,
                ]
            );

            return;
        }

        try {
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
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors[] = 'Тариф с таким названием уже существует.';
            } else {
                throw $exception;
            }

            $this->view->render(
                'tariffs/form',
                [
                    'title' => 'Создание тарифа',
                    'tariff' => $tariff,
                    'errors' => $errors,
                ]
            );

            return;
        }

        header('Location: /');
        exit;
    }

    public function edit(int $id): void
    {
        $tariff = $this->repository->findById($id);

        if ($tariff === null) {
            http_response_code(404);
            echo 'Тариф не найден';

            return;
        }

        $this->view->render(
            'tariffs/form',
            [
                'title' => 'Редактирование тарифа',
                'tariff' => $tariff,
                'errors' => [],
            ]
        );
    }

    public function update(int $id): void
    {
        $tariff = $this->repository->findById($id);

        if ($tariff === null) {
            http_response_code(404);
            echo 'Тариф не найден';

            return;
        }

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

        $tariff->name = $name;
        $tariff->description = $description !== '' ? $description : null;
        $tariff->speed = $speed;
        $tariff->price = $price;
        $tariff->expiresAt = $expiresAt !== ''
            ? date('Y-m-d H:i:s', strtotime($expiresAt))
            : null;

        if (!empty($errors)) {
            $this->view->render(
                'tariffs/form',
                [
                    'title' => 'Редактирование тарифа',
                    'tariff' => $tariff,
                    'errors' => $errors,
                ]
            );

            return;
        }

        try {
            $this->repository->update($tariff);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors[] = 'Тариф с таким названием уже существует.';
            } else {
                throw $exception;
            }

            $this->view->render(
                'tariffs/form',
                [
                    'title' => 'Редактирование тарифа',
                    'tariff' => $tariff,
                    'errors' => $errors,
                ]
            );

            return;
        }

        header('Location: /tariffs/' . $id);
        exit;
    }

    public function updateSpeed(int $id): void
    {
        $speed = (int) ($_POST['speed'] ?? 0);

        if ($speed <= 0) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'error' => 'Скорость должна быть больше 0.',
            ]);

            return;
        }

        $tariff = $this->repository->findById($id);

        if ($tariff === null) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'error' => 'Тариф не найден.',
            ]);

            return;
        }

        $this->repository->updateSpeed($id, $speed);

        header('Content-Type: application/json');

        echo json_encode([
            'success' => true,
            'speed' => $speed,
        ]);
    }

    public function exportCsv(): void
    {
        $tariffs = $this->repository->findAll();

        header('Content-Type: text/csv; charset=UTF-8');

        $filename = 'tariffs_' . date('Y-m-d_H-i-s') . '.csv';
        header(
            'Content-Disposition: attachment; filename="' . $filename . '"'
        );

        $output = fopen('php://output', 'w');

        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'id',
            'name',
            'description',
            'speed',
            'price',
            'created_at',
            'expires_at',
        ], ';');

        foreach ($tariffs as $tariff) {
            fputcsv($output, [
                $tariff->id,
                $tariff->name,
                $tariff->description,
                $tariff->speed,
                $tariff->price,
                $tariff->createdAt,
                $tariff->expiresAt,
            ], ';');
        }

        fclose($output);
        exit;
    }
}
