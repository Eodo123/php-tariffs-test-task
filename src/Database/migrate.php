<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Database\Database;

$databasePath = __DIR__ . '/../../database/database.sqlite';

$database = new Database($databasePath);

$sql = file_get_contents(
    __DIR__ . '/../../database/schema.sql'
);

$database->getConnection()->exec($sql);

echo "Database initialized successfully." . PHP_EOL;