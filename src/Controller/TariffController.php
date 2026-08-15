<?php

namespace App\Controller;

use App\Repository\TariffRepository;
use Dompdf\Dompdf;
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

    public function importCsv(): void
    {
        if (
            !isset($_FILES['file']) ||
            $_FILES['file']['error'] !== UPLOAD_ERR_OK
        ) {
            $_SESSION['error'] = 'Не удалось загрузить файл.';
            header('Location: /');
            exit;
        }

        $file = $_FILES['file']['tmp_name'];

        $handle = fopen($file, 'r');

        if ($handle === false) {
            $_SESSION['error'] = 'Не удалось открыть CSV-файл.';
            header('Location: /');
            exit;
        }

        $firstLine = fgets($handle);

        if ($firstLine === false) {
            fclose($handle);

            $_SESSION['error'] = 'CSV-файл пуст.';
            header('Location: /');
            exit;
        }

        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);

        $delimiter = str_contains($firstLine, ';') ? ';' : ',';

        rewind($handle);

        $bom = fread($handle, 3);

        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle, 0, $delimiter);

        $expectedHeader = [
            'id',
            'name',
            'description',
            'speed',
            'price',
            'created_at',
            'expires_at',
        ];

        if ($header !== $expectedHeader) {
            fclose($handle);

            $_SESSION['error'] = 'Неверный формат CSV-файла.';
            header('Location: /');
            exit;
        }

        if ($header === false) {
            fclose($handle);

            $_SESSION['error'] = 'CSV-файл пуст.';
            header('Location: /');
            exit;
        }

        $added = 0;
        $skipped = 0;
        $errors = [];

        $line = 1;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $line++;

            if (count($row) < 7) {
                $errors[] = "Строка {$line}: недостаточно данных.";
                continue;
            }

            [
                $id,
                $name,
                $description,
                $speed,
                $price,
                $createdAt,
                $expiresAt,
            ] = $row;

            $name = trim($name);

            if ($name === '') {
                $errors[] = "Строка {$line}: не указано название тарифа.";
                continue;
            }

            if ($this->repository->findByName($name) !== null) {
                $skipped++;
                $errors[] = "Строка {$line}: тариф «{$name}» уже существует.";
                continue;
            }

            if (!is_numeric($speed) || (int) $speed <= 0) {
                $errors[] = "Строка {$line}: некорректная скорость.";
                continue;
            }

            if (!is_numeric($price) || (float) $price < 0) {
                $errors[] = "Строка {$line}: некорректная стоимость.";
                continue;
            }

            if (!$this->isValidDate($createdAt)) {
                $errors[] = "Строка {$line}: некорректная дата создания.";
                continue;
            }

            if (
                trim($expiresAt) !== '' &&
                !$this->isValidDate($expiresAt)
            ) {
                $errors[] = "Строка {$line}: некорректная дата окончания.";
                continue;
            }

            try {
                $this->repository->create(
                    name: $name,
                    description: trim($description) !== ''
                        ? trim($description)
                        : null,
                    speed: (int) $speed,
                    price: (float) $price,
                    createdAt: $createdAt,
                    expiresAt: trim($expiresAt) !== ''
                        ? trim($expiresAt)
                        : null,
                );

                $added++;
            } catch (PDOException $exception) {
                if ($exception->getCode() === '23000') {
                    $skipped++;
                    $errors[] = "Строка {$line}: тариф «{$name}» уже существует.";
                } else {
                    fclose($handle);
                    throw $exception;
                }
            }
        }

        fclose($handle);

        $_SESSION['import_result'] = [
            'added' => $added,
            'skipped' => $skipped,
            'errors' => $errors,
        ];

        header('Location: /');
        exit;
    }

    public function exportPdf(): void
    {
        $tariffs = $this->repository->findAll();

        $html = '
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: DejaVu Sans, sans-serif;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                }

                th, td {
                    border: 1px solid #000;
                    padding: 5px;
                    text-align: left;
                }

                th {
                    background-color: #eee;
                }
            </style>
        </head>
        <body>
            <h1>Тарифы</h1>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Название</th>
                        <th>Описание</th>
                        <th>Скорость</th>
                        <th>Стоимость</th>
                        <th>Дата создания</th>
                        <th>Дата окончания</th>
                    </tr>
                </thead>
                <tbody>
    ';

        foreach ($tariffs as $tariff) {
            $html .= '
            <tr>
                <td>' . htmlspecialchars((string) $tariff->id) . '</td>
                <td>' . htmlspecialchars($tariff->name) . '</td>
                <td>' . htmlspecialchars($tariff->description ?? '') . '</td>
                <td>' . htmlspecialchars((string) $tariff->speed) . ' Мбит/с</td>
                <td>' . htmlspecialchars((string) $tariff->price) . ' ₽</td>
                <td>' . htmlspecialchars($tariff->createdAt) . '</td>
                <td>' . htmlspecialchars($tariff->expiresAt ?? '') . '</td>
            </tr>
        ';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>
    ';

        $dompdf = new Dompdf();

        $dompdf->loadHtml($html, 'UTF-8');

        $dompdf->setPaper('A4', 'landscape');

        $dompdf->render();

        $filename = 'tariffs_' . date('Y-m-d_H-i-s') . '.pdf';

        $dompdf->stream($filename, [
            'Attachment' => true,
        ]);
    }

    private function isValidDate(string $date): bool
    {
        $dateTime = \DateTime::createFromFormat(
            'Y-m-d H:i:s',
            $date
        );

        return $dateTime !== false
            && $dateTime->format('Y-m-d H:i:s') === $date;
    }
}
