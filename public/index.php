<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\TariffController;
use App\Database\Database;
use App\Repository\TariffRepository;
use App\Router\Router;
use App\View\View;

$database = new Database(
    __DIR__ . '/../database/database.sqlite'
);

$repository = new TariffRepository(
    $database->getConnection()
);

$view = new View();

$controller = new TariffController(
    $repository,
    $view
);

$router = new Router();

$router->get(
    '/',
    [$controller, 'index']
);

$router->get(
    '/tariffs/export/csv',
    [$controller, 'exportCsv']
);

$router->post(
    '/tariffs/import/csv',
    [$controller, 'importCsv']
);

$router->get(
    '/tariffs/create',
    [$controller, 'create']
);

$router->post(
    '/tariffs',
    [$controller, 'store']
);

$router->get(
    '/tariffs/{id}/edit',
    [$controller, 'edit']
);

$router->post(
    '/tariffs/{id}/speed',
    [$controller, 'updateSpeed']
);

$router->post(
    '/tariffs/{id}',
    [$controller, 'update']
);

$router->get(
    '/tariffs/{id}',
    [$controller, 'view']
);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);
